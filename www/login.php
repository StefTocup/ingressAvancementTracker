<?php


header('Content-Type: text/html; charset=ISO-8859-1'); 
// Gestion de la sécurité 
// Positionne les variable 
//  - $security ( paramètres de sécurité propre au compte ) 
//  - $bandeauLogin  ( bandeau de connexion/déconnexion )
require_once("MySQLConnect.php");

// Salt est récupéré par substr(md5(mt_rand()), 0, 4)

$action = @$_POST["action"];

$login      = "";
$passwd     = "";
$hashPasswd = "";

function hashPasswd( $login, $passwd )
{
	return sha1( strtolower($login) . $passwd);
}

function genere_bandeau_login( $login, $security )
{
	$bandeau  = "<span>\n";
	$bandeau .=  "<form method=POST ".(@$urlAction !="" ? "action=\"$urlAction\"" : "" ).">";		
	$bandeau .=  "<span>login</span>\n";
	if ( @$security["status"] == "Passed" )
	{
		$bandeau .=  "<span>$login</span>\n";
		$bandeau .=  "<span>\n";
		$bandeau .=  "<input name=action type=hidden value=\"logout\"></input>";
		$bandeau .=  "<input type=submit value=D&eacute;connexion></input>";
		$bandeau .=  "</span>\n";
	}
	else
	{

		$bandeau .=  "<span>";

		$bandeau .=  "<input name=login type=text value=\"$login\" />";
		$bandeau .=  "</span>\n";
		$bandeau .=  "<span>password</span>\n";
		$bandeau .=  "<span><input name=action type=hidden value=\"login\"></input>";
		$bandeau .=  "<input name=passwd type=password /></input></span>\n";
		$bandeau .=  "<span><input type=submit value=Connexion ></input></span>\n";
		$bandeau .=  "<span><input type=button value=Inscription onClick=\"window.location.href='inscription.php'\" ></input></span>\n";
		
		if ( @$security["status"] == "Failed")
		{
			$bandeau .=  "<td><color=red>login failed</color></td>";
		} 
		
	}
	$bandeau .=  "</form>\n";

	$bandeau .=  "</span>";
	$bandeau .=  "</span>";
	return $bandeau;
}

function genere_bandeau_login_table( $login, $security )
{
	$bandeau .= "<table>\n";
	$bandeau .=  "<tr><td>login</td>\n";
	if ( @$security["status"] == "Passed" )
	{
		$bandeau .=  "<td>$login</td>\n";

		$bandeau .=  "<td>\n";
		$bandeau .=  "<form method=POST ".($urlAction !="" ? "action=\"$urlAction\"" : "" ).">\n";
		$bandeau .=  "<input name=action type=hidden value=\"logout\" />";
		$bandeau .=  "<input type=submit value=D&eacute;connexion />";
		$bandeau .=  "</form>\n";
		$bandeau .=  "</td>\n";
	}
	else
	{
		$bandeau .=  "<form method=POST ".($urlAction !="" ? "action=\"$urlAction\"" : "" ).">\n";
		$bandeau .=  "<td>";
		
		$bandeau .=  "<input name=login type=text value=\"$login\" />";
		$bandeau .=  "</td>\n";
		$bandeau .=  "<td>password</td>\n";
		$bandeau .=  "<td><input name=action type=hidden value=\"login\" />";
		$bandeau .=  "<input name=passwd type=password /></input></td>\n";
		$bandeau .=  "<td><input type=submit value=Connexion /></td>\n";
		if ( $security["status"] == "Failed")
		{
			$bandeau .=  "<td><color=red>login failed</color></td>";
		} 
		
		$bandeau .=  "</form>";
	}
	$bandeau .=  "</tr>";
	$bandeau .=  "</table>";
	return $bandeau;
}

$security = array();

if ( $action == "logout" )
{
	// On procede au logout => on purge les cookies
	setcookie("login", "", time() - 3600 );
	setcookie("hashPasswd", "", time() - 3600 );
}
else
{
	// Le login est en priorité récupéré dans le cookie
	$login = @$_COOKIE["login"];
	// S'il n'est pas trouvé on regarde ce qui a été passé en POST
	if ( @$login == "" ) {	$login = @$_POST["login"]; }

	// Le passwd hashé à pu être sauvegardé dans les cookies
	$hashPasswd = @$_COOKIE["hashPasswd"];
	// si le passwd hashé n'existe pas on va regarder ce qui est passé comme argument
	if ( $hashPasswd == "" )
	{	$passwd = @$_POST["passwd"]; }
}

if ( $login != "" )
{
	if ( $passwd != "" )
	{
		$hashPasswd = ""; 
		//TODO : mysql_escape_string
		$sql = "select login from users where login = '".urldecode($login)."'";
		$res = mysql_query( $sql );
		if ( mysql_num_rows( $res ) > 0 )
		{
			if ( $row = mysql_fetch_array( $res ) )
			{
				$hashPasswd = hashPasswd( urldecode($login), $passwd );
			}
		} 
	}
	if ( $hashPasswd != "" )
	{
		//TODO : mysql_escape_string		
		$sql = "select * from users where login = '".urldecode($login)."' and passwd = '$hashPasswd'";
		$res = mysql_query( $sql );
		if ( $row = mysql_fetch_array( $res ) )
		{
			$security["status"]           = "Passed";
			$security["login"]            = $row["login"];
			$security["id_joueur"]        = $row["id_joueur"];
			// Positionnement du cookie et expiration dans 1 an
			setcookie( "login", $login, time() + 86400*30*12);
			setcookie( "hashPasswd", $hashPasswd, time() + 86400*30*12);
//			logLogin($row["login"]);
		}
		else
		{
			$security["status"] = "Failed";
			setcookie( "login", "", time() - 86400 );
			setcookie( "hashPasswd", "", time() - 86400);
//			logErreur( $login, "Erreur de login" );
		}
	}
}


$bandeauLogin = genere_bandeau_login( $login, $security );

//print_r( $security );

//phpinfo();
?>
