#!/usr/bin/python
# -*- coding: utf-8 -*-
# stdin
import sys
# lecture mail
import email
# Appel de l'ocr
import subprocess
# Regexp
import re
# Mysql
import MySQLdb
#conversion de date
from datetime import datetime
# envoi de mails
import smtplib
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
# suppression de fichier
import os
# Import de la config
from configIAT import *
cnx=None

if __name__ == "__main__":
	dateImport = ""
	filename = ""
	# Le mail a analyser est sur l'entrée standard
	message = email.message_from_file( sys.stdin );
	# Recherche de la piece jointe
	for i, part in enumerate(message.walk()):
		if part.get_content_maintype() == 'image':
			filename = part.get_filename()
			# Extraction du nom de la piece jointe de la date d'import
			
			if not filename:
				ext = mimetypes.guess_extension(part.get_content_type())
				filename = '/dev/null'
			else :
				m = re.search( 'profile_(\d{8}_\d{6})_\d+.png', filename)
				dateImport = m.group(1)
				dateImport = dateImport.replace( "_", " ")
				ts = datetime.strptime( dateImport, '%Y%m%d %H%M%S')
				dateImport = datetime.strftime( ts, '%Y-%m-%d %H:%M:%S' )
			# Sauvegarde de la piece jointe dans un fichier temporaire
			with open(tempDir+"/"+filename, 'wb') as fp:
				fp.write(part.get_payload(decode=True))
	
	if ( dateImport != "" and filename != "" ) :
		print dateImport
		# Appel de l'ocr sur l'image envoyée
		process = subprocess.Popen( ['tesseract', '-psm', '4' ,tempDir+"/"+filename, 'stdout'], stdout=subprocess.PIPE )
		stdout, stderr = process.communicate()
		os.remove( tempDir+"/"+filename )
		listeChampsOCR = dict()
		
		numligne = 0
		old_line=""
		# Analyse des données sortant de l'OCR
		# On constitue
		for line in stdout.split('\n'):
			# Le Pseudo se situe avant la ligne contenant le niveau
			# TODO : Postulat actuel, le pseudo n'a pas le droit de contenir d'espace, on filtre donc tous les caractères après un espace.
			m = re.search( '^LVL', line )
			if ( m ):
				# On supprime les caracteres fantaisistes en fin de ligne
				m = re.search( '^([^\s]+)', old_line )
				if ( m ):
					listeChampsOCR[ "Pseudo" ] = m.group( 1 )
			
			# 1,849,264 AP 12,400,000 AP-
			# 6,022,816 AP [6,000,000 AP
			m = re.search( '([\d,]+) AP .+ AP', line )
			if ( m ) :
				AP = m.group(1)
				AP = AP.replace(",", "" )
				listeChampsOCR[ "AP" ] = AP
				#print AP
			else :
				# Recherche des lignes standard
				# ex :Enemy Control Fields Destroyed 128
				#     Health 7 W
				#     Distance Walked 206 km
				#     Defense 
				#     Max Time Portal Held 36 days

				m = re.search( '(.*) ([\d,]+).*', line )
				if ( m ) :
					titre  = m.group( 1 )
					valeur = m.group( 2 )
					valeur = valeur.replace(",", "" )
					listeChampsOCR[ titre ] = valeur
					
					#print titre + "->" + valeur
			old_line = line
			
	print listeChampsOCR
			
	# Recherche dans la base ingress des champs à rechercher
	try:
		# Connexion a la base de donnée
		cnx = MySQLdb.connect(hostDB, userDB, passDB, nameDB)
		cur = cnx.cursor()
		# Vérification de l'existence de l'utilisateur dans la base
		sql = "SELECT id_joueur, mail from users where login='"+ listeChampsOCR["Pseudo"] +"'"
		cur.execute( sql )
		row = cur.fetchone()
		if ( row ):
			# Tableau de hashage contenant pour un type de donnée la valeur sortant de l'OCR, mais limité aux champs valides sortant 
			listeChamps   = dict()
			listeIDChamps = dict()

			id_joueur      = row[0]
			mail_parametre = row[1]
			listeChamps['Pseudo'] = listeChampsOCR["Pseudo"] 
			sql = "select id_compteur, lib_champ from compteurs order by id_compteur"
			cur.execute( sql )
			for row in cur.fetchall() :
				titre = row[1]
				listeIDChamps[ titre ] = row[0]
				if titre in listeChampsOCR:
					listeChamps[ titre ] = listeChampsOCR[ titre ]
				else :
					listeChamps[ titre ] = 0
					print titre + " n'est pas dans les champs de l'OCR, initialisé à 0"
			print "Importation utilisateur "+ listeChamps["Pseudo"]+ "; id :" + str(id_joueur)

			# Calcul des différentiels pour le mail de confirmation
			sql = "select date, lib_champ, valeur from historique h, compteurs c where c.id_compteur = h.id_compteur and  id_joueur="+ str(id_joueur)+" and date = (select max(date) from historique where id_joueur = "+str(id_joueur)+")"
			cur.execute( sql )

			listeDelta = dict()
			mailData = ""
			mailDataHTML ="<html><head>Rapport d'op&eacute;ration</head><body><table>"
			mailDataHTML +="<tr><td></td><td>Avant</td><td>Actuel</td><td>Variation</td></tr>"
			for row in cur.fetchall() :
				titre = row[1]
				date  = row[0]
				if ( not titre in listeChamps ) :
					listeChamps[ titre ] = 0
				
				listeDelta[ titre ] = int(listeChamps[ titre ]) - int(row[2])
				id_compteur = listeIDChamps[ titre ] 
				mailData += titre +": " + str(row[2]) +" => "+ str(listeChamps[ titre ]) +" : +" + str(listeDelta[ titre ])+"\n"
				mailDataHTML += "<tr><td>"+titre +"</td><td>" + str(row[2]) +"</td><td>"+ str(listeChamps[ titre ]) +"</td><td>+" + str(listeDelta[ titre ])+"</td></tr>\n"
				print titre +": " + str(row[2]) +" => "+ str(listeChamps[ titre ]) +" : +" + str(listeDelta[ titre ])
				sql = "insert into historique (id_joueur, date, id_compteur, valeur) values ({}, '{}', {}, {} ) ".format( id_joueur, dateImport, id_compteur,listeChamps[ titre ])
				cur.execute( sql )
			mailDataHTML += "</table></body></html>"

			# Envoi du mail de rapport
			if mail_parametre:
				msg = MIMEMultipart('alternative')
				msg['Subject'] = "Rapport d'opération"
				msg['From']    = 'iat.jarvis@gmail.com'
				msg['To']      = mail_parametre
				part1 = MIMEText(mailData, 'plain')
				part2 = MIMEText(mailDataHTML, 'html')
				msg.attach(part1)
				msg.attach(part2)
				print "cnx smtp.gmail.com"
				smtpserver = smtplib.SMTP("smtp.gmail.com",587)
				smtpserver.ehlo()
				smtpserver.starttls()
				smtpserver.ehlo
				print "login smtp.gmail.com"
				smtpserver.login(gmail_user, gmail_pwd)
				print "sendmail smtp.gmail.com"
				smtpserver.sendmail(msg['From'], msg['To'] , msg.as_string())
				smtpserver.close()

		else :
			print "Utilisateur inconnu, pas d'importation possible"
			print listeChamps
		
		
	except MySQLdb.Error, e:
	  
		print "Error %d: %s" % (e.args[0],e.args[1])
		sys.exit(1)
		
	finally:    
			
		if cnx:    
			cnx.close()
		# Analyse des évolutions dans les champs
		# Insertion dans la base 
		# Envoi d'un mail avec les évolutions 
