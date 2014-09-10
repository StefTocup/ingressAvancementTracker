<?php
	$nologin = 1;
	require_once("entete.php");
	require_once("MySQLConnect.php");
	$message       = "";
	$action        = $_POST["action"];
	$NewPasswd     = $_POST["NewPasswd"];
	$NewPasswdConf = $_POST["NewPasswdConf"];
	if (@$_POST["action"] == "inscription" )
	{
		$utilisateur = urldecode(@$_POST["utilisateur"]);
		if ( @$utilisateur == "") { $message = "Nom d'utilisateur incorrect"; }
		if ( $NewPasswd == ""  )	 { $message = "Le Mot de passe ne peut pas être vide"; }
		if ( $NewPasswd != $NewPasswdConf ) 	{	$message="Les deux mots de passe ne doivent pas être diff&eacute;rents<br/>";}
		if ( $message == "" )
		{
			echo strtolower($utilisateur) . $NewPasswd;
			$pass = sha1( strtolower($utilisateur) . $NewPasswd);
			$sql = "insert into users ( login, passwd, niveau, admin ) values( '".$utilisateur."','".$pass."' ,1, 0 )";
			mysql_query( $sql );
?>
			<h2>Inscription effectuée</h2>
			<a href="saisieAvancement.php"> Se connecter</a>
<?php
		}
		else
		{
			$action = "";
		}
		
	}
	if ($action == "")
	{
?>
<center>
	<span class=profil>
	<form method=POST action=inscription.php>
		<table border=0>
			<tr><td colspan=2><?php echo $message?></td></tr>
			<input type=hidden name=action value="inscription" />  
			<tr><td>Utilisateur</td><td><input type=text name=utilisateur><?php echo $utilisateur ?></input></td></tr>
			<tr><td>Mot de Passe</td><td><input type=password name=NewPasswd></input></td></tr>
			<tr><td>Confirmation Mot de Passe</td><td><input type=password name=NewPasswdConf></input> </td></tr>
			<tr><td></td><td><input type=submit value=Inscription /></td></tr>
		</table>
	</form>
	</span>
</center>
<?php
	}
	require_once("enqueue.php");
?>
