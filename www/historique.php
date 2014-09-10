<?php
	require_once("entete.php");
	require_once("MySQLConnect.php");


function affiche_ligne( $date, $liste_compteurs, $tableau_donnees )
{
	echo "<tr>";
	echo "<td>".$date."</td>";
	foreach ( $liste_compteurs as $id_compteur => $tableau )
	{
		echo "<td>".$tableau_donnees[$id_compteur]."</td>";
	}
	echo "</tr>";
}
	$id_joueur = @$security["id_joueur"];
	if ( $id_joueur != "" )
	{
		$sql = "select id_compteur, lib_champ, lib_medaille from compteurs order by id_compteur";
		$res = mysql_query( $sql );
		while ( $row = mysql_fetch_array( $res ) )
		{
			$liste_compteurs[ $row['id_compteur'] ][ "lib_champ"]       = $row['lib_champ'];
			$liste_compteurs[ $row['id_compteur'] ][ "lib_medaille"]    = $row['lib_medaille'];
		}
		
		$sql = "select date, id_compteur, valeur from historique where id_joueur = '$id_joueur' order by date, id_compteur";
		
		$res = mysql_query( $sql );
		$date_old = "";
		$tableau_ligne = array();
		echo "<table>";
		
		echo "<tr>";
		echo "<td>".$date."</td>";
		foreach ( $liste_compteurs as $id_compteur => $tableau )
		{
			echo "<td>".$tableau["lib_medaille"]."</td>";
		}
		echo "</tr>";
		
		while ( $row = mysql_fetch_array( $res ) )
		{
			if ( $date_old != "" and $date_old != $row["date"] )
			{
				affiche_ligne( $date_old, $liste_compteurs, $tableau_ligne );
				$tableau_ligne = array();
			}
			$tableau_ligne[ $row['id_compteur'] ] = $row['valeur'];
			$date_old = $row["date"];
		}
		affiche_ligne( $date_old , $liste_compteurs, $tableau_ligne );
		echo "</table>";
	}
?>

<?php
	require_once("enqueue.php");
?>
