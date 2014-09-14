<?php
	require_once("entete.php");
	require_once("MySQLConnect.php");

$compteur_ligne = 0;

function affiche_ligne( $date, $liste_compteurs, $tableau_donnees )
{
	global $compteur_ligne;
	$compteur_ligne++;
	$classe_ligne=( $compteur_ligne%2 == 0 ? "ligne_paire" : "ligne_impaire" );
	
	echo "<tr class=\"historique $classe_ligne\">";
	echo "<td>".$date."</td>";
	foreach ( $liste_compteurs as $id_compteur => $tableau )
	{
		echo "<td>".$tableau_donnees[$id_compteur]."</td>";
	}
	//echo "<td><a href=\"historique.php?action=delete&date=$date\"><img alt=\"Supprimer\" src=\"images/supprimer.png\" /></a></td>";
	echo "<td><input type=button value=Drop onclick=\"window.location.href='historique.php?action=delete&date=$date'\"></input></td>";
	echo "<td><input type=button value=Edit onclick=\"window.location.href='saisieAvancement.php?action=edit&date=$date'\"></input></td>";
	echo "</tr>";
}
	$id_joueur = @$security["id_joueur"];
	if ( $id_joueur != "" )
	{
		if ( @$_GET["action"] == "delete" )
		{
			$dateSupression = $mysqli->real_escape_string( @$_GET["date"] );
			// TODO : Vérification de la date ?
			$sql = "delete from historique where id_joueur=$id_joueur and date=\"$dateSupression\"";
			
			$res = $mysqli->query( $sql );
		}
		$sql = "select id_compteur, lib_champ, lib_medaille from compteurs order by id_compteur";
		$res = $mysqli->query( $sql );
		while ( $row = $res->fetch_assoc( ) )
		{
			$liste_compteurs[ $row['id_compteur'] ][ "lib_champ"]       = $row['lib_champ'];
			$liste_compteurs[ $row['id_compteur'] ][ "lib_medaille"]    = $row['lib_medaille'];
		}
		
		$sql = "select date, id_compteur, valeur from historique where id_joueur = '$id_joueur' order by date DESC , id_compteur";
		
		$res = $mysqli->query( $sql );
		$date_old = "";
		$tableau_ligne = array();
		echo "<table>";
		
		echo "<tr>";
		echo "<td>date</td>";
		foreach ( $liste_compteurs as $id_compteur => $tableau )
		{
			echo "<td>".$tableau["lib_medaille"]."</td>";
		}
		echo "</tr>";
		
		while ( $row = $res->fetch_assoc() )
		{
			if ( $date_old != "" and $date_old != $row["date"] )
			{
				affiche_ligne( $date_old, $liste_compteurs, $tableau_ligne );
				$tableau_ligne = array();
			}
			$tableau_ligne[ $row['id_compteur'] ] = $row['valeur'];
			$date_old = $row["date"];
		}
		if ( count( $tableau_ligne ) > 0 )
		{
			affiche_ligne( $date_old , $liste_compteurs, $tableau_ligne );
		}
		else
		{
			echo "<td colspan=10><h2>Aucune donn&eacute;es</h2></td>";
		}
		echo "</table>";
	}
?>

<?php
	require_once("enqueue.php");
?>
