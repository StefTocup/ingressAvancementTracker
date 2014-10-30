#!/usr/bin/python
# -*- coding: utf8 -*-
from PIL import Image, ImageColor 



if __name__ == "__main__":
	fichierImageIn = "testIn.png"
	image = Image.open(fichierImageIn)
	fichierImageOut = "testOut.png"

	print fichierImageIn
	pix = image.load()
	

	# Suppression du logo 
	lowX=9999
	lowY=9999
	# Recherche de la ligne du sommet de l'écusson
	for x in range(0,200):
		for y in range(50,160):
			if ( pix[x,y][0] == 0 
				and pix[x,y][1] == 239 
				and pix[x,y][2] == 115 ):
					if y < lowY :
						lowX = x
						lowY = y

	#print str(lowX)+";"+str(lowY)
	pix[lowX, lowY] = 255
	
	highX=0
	for x in range( lowX, image.size[0]):
		print str(x)+";"+str(lowY)+";"+str(pix[x, lowY])
		if ( pix[x, lowY][0] == 0 and pix[x, lowY][1] == 0 and pix[x, lowY][2] == 0 and highX == 0):
			highX = y
			break
	#print str(highX)+";"+str(lowY)
	pix[highX, lowY] = 255
	# Paint it Black !
	for x in range ( 0, highX + 10):
		for y in range(lowY, lowY + (highX-0) ):
			pix[x, y] = 0
			
			
	# Suppression du bandeau du sommet
	x = 30
	highY = 0
	for y in range (0, image.size[1]):
		if ( pix[x, y][0] == 0 and pix[x, y][1] == 158 and pix[x, y][2] == 156 ):
			highY = y
			break
	
	print highY
	try:
		for x in range ( 0, image.size[0]-1 ):
			for y in range(0, highY + 5 ):
				pix[x, y] = 0
	except IndexError:
		print str(x)+";"+str(y)

	#recherche du logo g+


	image.save( fichierImageOut )
