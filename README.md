ingressAvancementTracker
========================

Installation
===========
Il faut créer la base ingress avec un mysqladmin create ingress.
modifier le script db/create.sql avec le mot de passe de l'utilisateur qui acceder.
Ce mot de passe est à reporter dans www/MySQLConnect.php

Prérequis facultatifs
=====================

Pour l'import par mail il faut :
apt-get install python-mysqldb tesseract

* un serveur de mail fonctionnel 
* un procmail avec une regle :
:0 c
* ^Delivered-To: .*iat.jarvis@gmail.com
| /home/stef/Prog/ingress/bin/iatMail.py >> /home/stef/Prog/ingress/log/iatMail.log 2>&1
