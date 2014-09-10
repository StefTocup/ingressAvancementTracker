<?php
	require_once("entete.php");
	require_once("MySQLConnect.php");

	$login            = urldecode(@$_POST["login"]);
	$dateArrivee      = urldecode(@$_POST["dateArrivee"]);
	$mail             = urldecode(@$_POST["mail"]);
	$password         = hashPasswd( $login, urldecode( @$_POST["password"] ) );

	$chaineValidation = md5( $login  + time() ) ;
	
	if ( $login != "" )
	{
		if ( $security["adminRoliste"] == 1 or $security["adminFiguriniste"] == 1  )
	//TODO : mysql_escape_string
		$sql = "insert into users( login, passwd, valide,  mail,  chaineValidation ) 
				values( '$login', '$password', 1, '$mail', '$chaineValidation')";
		$res = mysql_query( $sql );
		if ( $res )
			print ("<div> $login ajouté </div>");
		else
			print ("<div>".mysql_error()."</div>");
	}

/* Affichage du formulaire */
?>
<form method=POST>
	<table>
		<tr><td>Joueur &agrave; ajouter</td><td><input type=text name=login /></td></tr>
		<tr><td>Password</td><td><input type=password name=password /></td></tr>
		<tr><td>mail</td><td><input type=text name=mail /></td></tr>

		<tr><td colspan=2> <input type=submit value="Ajouter"></td></tr>
	</table>
</form>

<? require_once("enqueue.php") ?>
