<?php 
	if ( @$nologin != 1 ) { require_once("login.php"); }
//<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
//<html xmlns="http://www.w3.org/1999/xhtml">
?>

<html>
<head>
<title><?php echo @$title ?> </title>
<meta name="Robots" content="noindex,nofollow" />
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<link rel="stylesheet" type="text/css" media="print,screen" href="main.css" />
<link rel="stylesheet" type="text/css" media="print" href="print.css" />
</head>

<?php
	echo '<body>';
	/* Affichage du menu */
	if ( @$_GET["dontShowMenu"] != 1 && @$dontShowMenu != 1 )
	{
		// Espace de gestion du compte
		if ( @$security["status" ] == "Passed" )
		{
			print ("<span><a href=\"modifUtilisateur.php?utilisateur=".$security["login"]."\">Profil</a></span>\n");
		}
	
		print ("<span><a href=saisieAvancement.php>Saisie Avancement</a></span>\n");
		print ("<span><a href=medals.php>Medailles</a></span>\n");
		print ("<span><a href=niveaux.php>Niveaux</a></span>\n");
		print ("<span><a href=historique.php>Historique</a></span>\n");
		print ("<span><a href=statistiques.php>Statistiques</a></span>\n");
		print ("<span><a href=graphiques.php>Graphiques</a></span>\n");
		
	}
	if ( @$_GET["dontShowLogin"] != 1 && @$dontShowLogin != 1 )
	{
		print ("<span>".@$bandeauLogin."</span>\n");
	}
?>
<div>
