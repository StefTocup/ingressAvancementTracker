<?php
	require_once("entete.php");
	require_once("MySQLConnect.php");
	
function affiche_ligne( $lib_medaille,  $tableau_ligne, $valeur_actuelle = 0)
{
	echo "<tr>";
	echo "<td>".$lib_medaille."</td>";
	foreach ( $tableau_ligne as $valeur )
	{
		if ( $valeur_actuelle >= $valeur )
		{
			$classe = "highlight";
		}
		else
		{
			$classe = "";
		}
		echo "<td class=\"$classe\">".$valeur."</td>";
		
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
	//affiche_ligne( $old_lib_medaille, $tableau_ligne );
	affiche_ligne( $old_lib_medaille, $tableau_ligne, @$liste_compteurs[ $old_id_compteur ][ "derniere_valeur"] );
	echo "</table>";
?>
</center>
<?php
	require_once("enqueue.php");
?>
