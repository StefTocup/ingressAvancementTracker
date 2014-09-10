<?php
	require_once("entete.php");
	require_once("MySQLConnect.php");

// Calcul des médailles courantes et des objectifs
function calcule_medailles( &$liste_compteurs )
{
	$sql = "select pm.id_compteur, cm.lib_couleur_medaille, cm.url_couleur_medaille, pm.nb_min, pm.id_couleur_medaille
			from palliers_medailles pm, couleur_medaille cm 
			where pm.id_couleur_medaille = cm.id_couleur_medaille 
			order by pm.id_compteur, pm.nb_min";
			
	$res = mysql_query( $sql );
	while ( $row = mysql_fetch_array( $res ) )
	{
		$id_compteur = $row['id_compteur'];
		// Si la valeur correspondant au compteur est supérieure à la valeur de la médaille
		if ( $liste_compteurs[ $id_compteur ][ "derniere_valeur"] >= $row[ 'nb_min' ] )
		{
			$liste_compteurs[ $id_compteur ][ "couleur_medaille" ]    = $row[ 'lib_couleur_medaille' ] ;
			$liste_compteurs[ $id_compteur ][ "url_couleur_medaille" ]        = $row[ 'url_couleur_medaille' ] ;
			$liste_compteurs[ $id_compteur ][ "nb_min" ]              = $row[ 'nb_min' ] ;
			$liste_compteurs[ $id_compteur ][ "id_couleur_medaille" ] = $row[ 'id_couleur_medaille' ] ;
		}
		else
		{
			// on stocke le pallier strictement au dessus de la médaille courante, ce sera l'objectif pour la progression
			if( ! $liste_compteurs[ $id_compteur ][ "nb_max" ] )
			{
				$liste_compteurs[ $id_compteur ][ "nb_max" ]  = $row[ 'nb_min' ] ;
			}
		}
	}
	
	
}
	
function calcule_niveau( $liste_compteurs  )
{
	$niveau = 1;
	// décompte nombre médailles
	$nb_black=0;$nb_pt=0; $nb_au=0; $nb_ag=0; $nb_br=0;
	foreach ( $liste_compteurs as $id_compteur => $tableau )
	{
		switch ( $tableau["id_couleur_medaille"] )
		{
			case 5 : $nb_black ++;
			case 4 : $nb_pt ++;
			case 3 : $nb_au ++;
			case 2 : $nb_ag ++;
			case 1 : $nb_br ++;
		}
	}
//	print_r(array( "nb_ag" => $nb_ag,  "nb_au" => $nb_au,  "nb_pt" => $nb_pt,  "nb_black" => $nb_black) );
	$sql = "select * from niveaux order by niveau";
	$res = mysql_query( $sql );
	while ( $row = mysql_fetch_array( $res ) )
	{
		$diff_nb_black = $row["nb_black"] - $nb_black;  $diff_nb_black = ( $diff_nb_black > 0? $diff_nb_black : 0 );
		$diff_nb_pt    = $row["nb_pt"] - $nb_pt; $diff_nb_pt = ( $diff_nb_pt > 0 ? $diff_nb_pt : 0 );
		$diff_nb_au    = $row["nb_au"] - $nb_au; $diff_nb_au = ( $diff_nb_au > 0 ? $diff_nb_au : 0 );
		$diff_nb_ag    = $row["nb_ag"] - $nb_ag; $diff_nb_ag = ( $diff_nb_ag > 0 ? $diff_nb_ag : 0 );
		if ( $liste_compteurs["0"]["derniere_valeur"] >= $row["AP"] and $diff_nb_black <= 0 and $diff_nb_pt <= 0 and $diff_nb_au <=0  and $diff_nb_ag <= 0 )
		{
			$niveau = $row['niveau'];
		}
		else
		{
			return array( "niveau" => $niveau, "AP_necessaire"=> $row["AP"], "medailles" => array( "nb_ag" => $diff_nb_ag,  "nb_au" => $diff_nb_au,  "nb_pt" => $diff_nb_pt,  "nb_black" => $diff_nb_black) );
		}
	}
	return array( "niveau" => $niveau, "AP_necessaire"=> $row["AP"], "medailles" => array( "nb_ag" => $diff_nb_ag,  "nb_au" => $diff_nb_au,  "nb_pt" => $diff_nb_pt,  "nb_black" => $diff_nb_black) );
}

	if ( @$security['id_joueur'] != "" )
	{
		// Liste des compteurs et des médailles
		$sql = "select id_compteur, lib_champ, lib_medaille from compteurs order by id_compteur";
		$res = mysql_query( $sql );
		while ( $row = mysql_fetch_array( $res ) )
		{
			$liste_compteurs[ $row['id_compteur'] ][ "lib_champ"]       = $row['lib_champ'];
			$liste_compteurs[ $row['id_compteur'] ][ "lib_medaille"]    = $row['lib_medaille'];
			$liste_compteurs[ $row['id_compteur'] ][ "derniere_valeur"] = 0;
		}
		$sql = "select date, id_compteur, valeur from historique where id_joueur='".$security["id_joueur"]."' and date = (select max(date) from historique where id_joueur = '".$security["id_joueur"]."')";
		$res = mysql_query( $sql );
		$datePrecedenteSaisie="";
		while ( $row = mysql_fetch_array( $res ) )
		{
			$liste_compteurs[ $row['id_compteur'] ][ "derniere_valeur"] = $row['valeur'];
			$datePrecedenteSaisie = $row['date'];
		}
		if ( @$_POST['action'] == "saisie" )
		{
			$dateActuelle = date( "Y-m-d H:i:s" );
			foreach ( $liste_compteurs as $id_compteur => $tableau)
			{
				$valeur = @$_POST['compteur_'.$id_compteur];
				if ( $valeur == "" ) { $valeur = 0; }
				$delta[ $id_compteur ] = $_POST['compteur_'.$id_compteur] - $liste_compteurs[ $id_compteur ][ "derniere_valeur"] ;
				$sql = "insert into historique (date, id_joueur, id_compteur, valeur) values ( '$dateActuelle', ".$security['id_joueur'].", $id_compteur, $valeur)";
				$res = mysql_query( $sql );
			}
		}
?>
<center>
<form method="post" enctype="multipart/form-data">
<?php	
		
		// Récupération de la dernière liste de valeurs saisies par l'utilisateur.
		$sql = "select id_compteur, valeur from historique where id_joueur='".$security["id_joueur"]."' and date = (select max(date) from historique where id_joueur = '".$security["id_joueur"]."')";
		$res = mysql_query( $sql );
		while ( $row = mysql_fetch_array( $res ) )
		{
			$liste_compteurs[ $row['id_compteur'] ][ "derniere_valeur"] = $row['valeur'];
		}
		
		calcule_medailles( $liste_compteurs );
		$tableau_niveau = calcule_niveau( $liste_compteurs );
		$niveau = $tableau_niveau["niveau"];
		echo "<center>";
		echo "<table>";
		foreach ( $liste_compteurs as $id_compteur => $tableau)
		{
			echo "<tr>";
			if ( @$delta[ $id_compteur ] > 0 )
			{
				echo "<td>+".$delta[ $id_compteur ]."</td>";
			}
			else
			{
				echo "<td>&nbsp;</td>";
			}
			echo "<td>".$tableau["lib_champ"]."</td>";
			echo "<td><input type=\"text\" name=\"compteur_$id_compteur\" value=\"".$tableau["derniere_valeur"]."\"></input></td>";
			// Cas spécifique de l'AP
			if ( $id_compteur == 0 )	
			{	
				echo "<td>Niveau $niveau</td>";	
				$tableau["nb_max"] = $tableau_niveau["AP_necessaire"];
			}
			else
			{
				if ( @$tableau["url_couleur_medaille"] ) {	echo "<td><img src=\"".$tableau["url_couleur_medaille"]."\"></td>";		}
				else 							{	echo "<td>&nbsp;</td>";		}
			}
			$pourcentage=100.0 * $tableau["derniere_valeur"] / $tableau["nb_max"];
			$classe=($pourcentage >= 80 ? "highlight" : "");
			
			echo "<td class=\"$classe\">".sprintf("%0.2f", $pourcentage)."%</td>";
			echo "<td>".($tableau["derniere_valeur"] - $tableau["nb_max"])."</td>";

			if ( $id_compteur == 0 )
			{
				echo "<td>";
				if ($tableau_niveau["medailles"]["nb_ag"] > 0 ) { echo $tableau_niveau["medailles"]["nb_ag"]."x<img src=images/silver.png></img>"; }
				if ($tableau_niveau["medailles"]["nb_au"] > 0 ) { echo $tableau_niveau["medailles"]["nb_au"]."x<img src=images/gold.png></img>"; }
				if ($tableau_niveau["medailles"]["nb_pt"] > 0 ) { echo $tableau_niveau["medailles"]["nb_pt"]."x<img src=images/platinum.png></img>"; }
				if ($tableau_niveau["medailles"]["nb_black"] > 0 ) { echo $tableau_niveau["medailles"]["nb_black"]."x<img src=images/black.png></img>"; }
				echo "</td>";
			}
			echo "</tr>";
		}
		echo "</table>";
		echo "<input type=submit value=Valider />";
	
?>	
<input type=hidden name=action value=saisie>
</form>
</center>
<?php
	}
	require_once("enqueue.php");
?>
