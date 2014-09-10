<?php
	require_once("entete.php"); 
	require_once("MySQLConnect.php");
	echo '<script type="text/javascript" src="associationJeuxMJ.js"></script>';
	$action         = urldecode(@$_POST["action"]);
	$utilisateur    = urldecode(@$_POST["utilisateur"]);
	if( $utilisateur == "" ) 
	{
		$utilisateur    = urldecode(@$_GET["utilisateur"]);
	}
	if( $utilisateur == "" ) 
	{
		$utilisateur    = $security["login"];
	}	
	$NewPasswd		= urldecode(@$_POST["NewPasswd"]);
	$NewPasswdConf  = urldecode(@$_POST["NewPasswdConf"]);

	require_once("libTranscode.php");
	$utilisateur    = decode_chaine ( $utilisateur );

	//Seul les admins ont le droit de modifier un utilisateur différent du leur
	if ( @$security["admin"] == 1 
	or $security["login"] == $utilisateur)
	{
		if ( $NewPasswd != "" and $NewPasswd != $NewPasswdConf )
		{
			print("Les deux mots de passe sont diff&eacute;rents<br/>");
		}
		else if ( $action == "modif" )
		{
			// Pr�paration de la requete de Mise � jour
			$sqlUpdate = "update users set ";
		
			if ( $NewPasswd != "" )
			{


				$sqlSelect = "select login from users where login = '".$utilisateur."'";
				$res = $mysqli->query($sqlSelect);

				if ( $row = $res->fetch_assoc() )
				{
					$login = $row["login"];
					$NewPasswd = hashPasswd( urldecode($login), $NewPasswd );
					$sqlUpdate .= "$sep passwd = '$NewPasswd' ";
					$sep=",";
				}
			}

			if ( $utilisateur != "" )
			{
				$sqlUpdate .= " where login = '".$mysqli->real_escape_string( $utilisateur )."' ";
				$mysqli->query( $sqlUpdate );
			}

		}
	// Récupération des données dans la base
	$sqlSelect = "select * from users where login = '".$mysqli->real_escape_string( $utilisateur )."' ";

	$res = $mysqli->query( $sqlSelect );
	if ( $row = $res->fetch_assoc() )
	{
		$utilisateur = $row["login"];
	}
	else
	{
		$utilisateur = "";
	}



	if ( $utilisateur != "" )
	{
	
?>
<center>
	<span class=profil>
	<form method=POST action=modifUtilisateur.php>
		<table border=0>
			<input type=hidden name=utilisateur value="<?php echo $utilisateur?>" />
			<input type=hidden name=action value="modif" />  
			<tr><td>Utilisateur</td><td><?php echo $utilisateur?></td></tr>
			<tr><td>Mot de Passe</td><td><input type=password name=NewPasswd></input></td></tr>
			<tr><td>Confirmation Mot de Passe</td><td><input type=password name=NewPasswdConf></input> </td></tr>
			<tr><td></td><td><input type=submit value=Modifier /></td></tr>
		</table>
	</form>
	</span>
</center>
<?php		
		}
	}
	else
	{
		echo "Vous n'avez pas les droits pour consulter cette page";
	}
	

?>
<?php require_once("enqueue.php") ?>
