<?php
	require_once("entete.php");
	require_once("MySQLConnect.php");
	
echo "<center>";
	echo "<table>"; 
	
	// Affichage des entetes de colonne
	$sql = "select id_couleur_medaille, url_couleur_medaille from couleur_medaille";
	$res = $mysqli->query( $sql );
	$listeCouleursMedailles = array();
	while ( $row = $res->fetch_assoc() )
	{
		$listeCouleursMedailles[ $row["id_couleur_medaille"] ] = $row["url_couleur_medaille"];
	}
	echo "<table>";
	echo "<tr>";
	echo "<td>Niveau</td>";
	echo "<td>AP</td>";
	echo "<td>Pr&eacute; requis</td>";
	$sql = "select * from niveaux order by niveau";
	$res = $mysqli->query( $sql );
	while ( $row = $res->fetch_assoc() )
	{
		echo "<tr><td>".$row["niveau"]."</td><td>".$row["AP"]."</td><td>";
		if ( $row["nb_ag"] > 0) { echo $row["nb_ag"]."x <img src=\"".$listeCouleursMedailles[ 2 ]."\">"; }
		if ( $row["nb_au"] > 0) { echo $row["nb_au"]."x <img src=\"".$listeCouleursMedailles[ 3 ]."\">"; }
		if ( $row["nb_pt"] > 0) { echo $row["nb_pt"]."x <img src=\"".$listeCouleursMedailles[ 4 ]."\">"; }
		if ( $row["nb_black"] > 0) { echo $row["nb_black"]."x <img src=\"".$listeCouleursMedailles[ 5 ]."\">"; }

		echo "</td></tr>";
	}
	echo "</table>";
?>
</center>
<?php
	require_once("enqueue.php");
?>
