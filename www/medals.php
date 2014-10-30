<?php
	require_once("entete.php");
	require_once("MySQLConnect.php");
	
function affiche_ligne( $lib_medaille,  $tableau_ligne, $valeur_actuelle = 0)
{
	echo "<tr>";
	echo "<td>".$lib_medaille."</td>";
	$valeur_min = 0;
	$valeur_max = "";
	foreach ( $tableau_ligne as $valeur )
	{
		if ( $valeur_actuelle >= $valeur )
		{
			$valeur_min = $valeur;
			$classe = "highlight";
		}
		else
		{
			if ( $valeur_max == "") { $valeur_max = $valeur; }
			$classe = "";
		}
		echo "<td class=\"$classe\">".$valeur."</td>";
		
	}
	if ( $valeur_actuelle != 0)
	{
		if ( $valeur_max != "" )
			{
				$pourcentage_intra_niveau = 100.0 * ( $valeur_actuelle - $valeur_min) / ( $valeur_max - $valeur_min);
				echo "<td alt=\"progression\"><div class=\"progressbar niveau\"><div style=\"width: ".sprintf("%0.2f", $pourcentage_intra_niveau)."%\"></div></td>";
				echo "<td>($valeur_actuelle)</td>";
				
			}
	}
	
	echo "</tr>";
}

echo "<center>";
	echo "<table>"; 
	
	// Affichage des entetes de colonne
	$sql = "select url_couleur_medaille from couleur_medaille where id_couleur_medaille > 0 order by id_couleur_medaille";
	$res = $mysqli->query( $sql );
	echo "<tr>";
	echo "<td>Medailles</td>";
	while ( $row = $res->fetch_assoc() )
	{
		echo "<td><img src=\"".$row["url_couleur_medaille"]."\"></td>";
	}
	echo "</tr>";
	
	$sql = "select id_compteur, valeur from historique where id_joueur='".$security["id_joueur"]."' and date = (select max(date) from historique where id_joueur = '".$security["id_joueur"]."')";
	$res = $mysqli->query( $sql );
	while ( $row = $res->fetch_assoc() )
	{
		$liste_compteurs[ $row['id_compteur'] ][ "derniere_valeur"] = $row['valeur'];
	}
	
	$sql = "select c.id_compteur, pm.nb_min, c.lib_medaille, cm.id_couleur_medaille
			from palliers_medailles pm, couleur_medaille cm, compteurs c
			where pm.id_compteur = c.id_compteur
			and   pm.id_couleur_medaille = cm.id_couleur_medaille
			order by pm.id_compteur, cm.id_couleur_medaille";
	
	$res = $mysqli->query( $sql );

	$old_lib_medaille = "";
	$entete_affichee = 0;
	$tableau_ligne = array();	
	//print_r( $liste_compteurs );
	while ( $row = $res->fetch_assoc() )
	{
		if ( $old_lib_medaille != "" and $old_lib_medaille != $row["lib_medaille"] )
		{
			affiche_ligne( $old_lib_medaille, $tableau_ligne, @$liste_compteurs[ $old_id_compteur ][ "derniere_valeur"] );
			$tableau_ligne = array();
		}
		$tableau_ligne[ $row["id_couleur_medaille"] ] = $row["nb_min"];
		$old_lib_medaille = $row["lib_medaille"];
		$old_id_compteur = $row["id_compteur"];
	}
	affiche_ligne( $old_lib_medaille, $tableau_ligne, @$liste_compteurs[ $old_id_compteur ][ "derniere_valeur"] );
	echo "</table>";
?>
</center>
<?php
	require_once("enqueue.php");
?>
