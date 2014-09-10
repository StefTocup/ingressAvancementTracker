<?php
	require_once("entete.php");
	require_once("MySQLConnect.php");

// Calcul des médailles courantes et des objectifs
function calcule_medailles( &$mysqli, &$liste_compteurs )
{
	$sql = "select pm.id_compteur, cm.lib_couleur_medaille, cm.url_couleur_medaille, pm.nb_min, pm.id_couleur_medaille
			from palliers_medailles pm, couleur_medaille cm 
			where pm.id_couleur_medaille = cm.id_couleur_medaille 
			order by pm.id_compteur, pm.nb_min";
			
	$res = $mysqli->query( $sql );
	while ( $row = $res->fetch_assoc() )
	{
		$id_compteur = $row['id_compteur'];
		// Si la valeur correspondant au compteur est supérieure à la valeur de la médaille
		if ( $liste_compteurs[ $id_compteur ][ "derniere_valeur"] >= $row[ 'nb_min' ] )
		{
			$liste_compteurs[ $id_compteur ][ "couleur_medaille" ]    = $row[ 'lib_couleur_medaille' ] ;
			$liste_compteurs[ $id_compteur ][ "url_couleur_medaille" ]= $row[ 'url_couleur_medaille' ] ;
			$liste_compteurs[ $id_compteur ][ "nb_min" ]              = $row[ 'nb_min' ] ;
			$liste_compteurs[ $id_compteur ][ "id_couleur_medaille" ] = $row[ 'id_couleur_medaille' ] ;
		}
		else
		{
			// on stocke le pallier strictement au dessus de la médaille courante, ce sera l'objectif pour la progression
			if( ! @$liste_compteurs[ $id_compteur ][ "nb_max" ] )
			{
				$liste_compteurs[ $id_compteur ][ "nb_max" ]  = $row[ 'nb_min' ] ;
				$liste_compteurs[ $id_compteur ][ "couleur_medaille_suivante" ]  = $row[ 'lib_couleur_medaille' ] ;
				$liste_compteurs[ $id_compteur ][ "url_medaille_suivante" ]      = $row[ 'url_couleur_medaille' ] ;
			}
		}
	}
	
	
}
	
function calcule_niveau( &$mysqli, $liste_compteurs  )
{
	$niveau = 1;
	// décompte nombre médailles
	$nb_black=0;$nb_pt=0; $nb_au=0; $nb_ag=0; $nb_br=0;
	foreach ( $liste_compteurs as $id_compteur => $tableau )
	{
		switch ( @$tableau["id_couleur_medaille"] )
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
	$res = $mysqli->query( $sql );
	while ( $row = $res->fetch_assoc() )
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
			return array( "niveau" => $niveau, "AP_necessaire"=> $row["AP"], 
				"medailles_necessaire" => array( "nb_ag" => $diff_nb_ag,  "nb_au" => $diff_nb_au,  "nb_pt" => $diff_nb_pt,  "nb_black" => $diff_nb_black) ,
				"medailles" 	=> array( "nb_br" => $nb_br, "nb_ag" => $nb_ag,  "nb_au" => $nb_au,  "nb_pt" => $nb_pt,  "nb_black" => $nb_black) );
		}
	}
	return array( "niveau" => $niveau, "AP_necessaire"=> $row["AP"], 
				"medailles_necessaire" => array( "nb_ag" => $diff_nb_ag,  "nb_au" => $diff_nb_au,  "nb_pt" => $diff_nb_pt,  "nb_black" => $diff_nb_black) ,
				"medailles" 	=> array( "nb_br" => $nb_br, "nb_ag" => $nb_ag,  "nb_au" => $nb_au,  "nb_pt" => $nb_pt,  "nb_black" => $nb_black) );
}

	if ( @$security['id_joueur'] != "" )
	{
		// Liste des compteurs et des médailles
		$sql = "select id_compteur, lib_champ, lib_medaille from compteurs order by id_compteur";
		$res = $mysqli->query( $sql );
		while ( $row = $res->fetch_assoc() )
		{
			$liste_compteurs[ $row['id_compteur'] ][ "lib_champ"]       = $row['lib_champ'];
			$liste_compteurs[ $row['id_compteur'] ][ "lib_medaille"]    = $row['lib_medaille'];
			$liste_compteurs[ $row['id_compteur'] ][ "derniere_valeur"] = 0;
		}
		
		$sql = "select date, id_compteur, valeur from historique where id_joueur='".$security["id_joueur"]."' and date = ";
		if ( @$_GET['action'] == "edit" and @$_GET['date'] )
		{	$sql = "select date, id_compteur, valeur from historique where id_joueur='".$security["id_joueur"]."' and date = \"".@$_GET['date']."\"";	}
		else
		{	$sql = "select date, id_compteur, valeur from historique where id_joueur='".$security["id_joueur"]."' and date = (select max(date) from historique where id_joueur = '".$security["id_joueur"]."')";	}

		$res = $mysqli->query( $sql );
		$datePrecedenteSaisie="";
		while ( $row = $res->fetch_assoc() )
		{
			$liste_compteurs[ $row['id_compteur'] ][ "derniere_valeur"] = $row['valeur'];
			$datePrecedenteSaisie = $row['date'];
		}
		if ( @$_POST['action'] == "saisie" )
		{
			// Gestion de l'insertion antidatée des données
			if ( @$_POST["dateActuelle"] )	{	$dateActuelle = $_POST["dateActuelle"];		}
			else							{	$dateActuelle = date( "Y-m-d H:i:s" );		}
			
			foreach ( $liste_compteurs as $id_compteur => $tableau)
			{
				$valeur = @$_POST['compteur_'.$id_compteur];
				if ( $valeur == "" ) { $valeur = 0; }
				$delta[ $id_compteur ] = $_POST['compteur_'.$id_compteur] - $liste_compteurs[ $id_compteur ][ "derniere_valeur"] ;
				$sql = "replace into historique (date, id_joueur, id_compteur, valeur) values ( '$dateActuelle', ".$security['id_joueur'].", $id_compteur, $valeur)";
				$res = $mysqli->query( $sql );
			}
		}
?>
<center>
<form method="post" enctype="multipart/form-data">
<?php	
		
		// Récupération de la dernière liste de valeurs saisies par l'utilisateur.
		if ( @$_GET['action'] == "edit" and @$_GET['date'] )
		{	$sql = "select date, id_compteur, valeur from historique where id_joueur='".$security["id_joueur"]."' and date = \"".@$_GET['date']."\"";	}
		else
		{	$sql = "select date, id_compteur, valeur from historique where id_joueur='".$security["id_joueur"]."' and date = (select max(date) from historique where id_joueur = '".$security["id_joueur"]."')";	}
		
		//$sql = "select id_compteur, valeur from historique where id_joueur='".$security["id_joueur"]."' and date = (select max(date) from historique where id_joueur = '".$security["id_joueur"]."')";
		$res = $mysqli->query( $sql );
		while ( $row = $res->fetch_assoc() )
		{
			$liste_compteurs[ $row['id_compteur'] ][ "derniere_valeur"] = $row['valeur'];
		}
		
		calcule_medailles( $mysqli, $liste_compteurs );
		$tableau_niveau = calcule_niveau( $mysqli, $liste_compteurs );
		$niveau = $tableau_niveau["niveau"];
		echo "<center>";
		echo "<table>";
		echo "<tr>";
		echo "<td>&nbsp;</td>";
		echo "<th>Date et heure (facultatif)</th>";
		echo "<td><input type=\"text\" name=\"dateActuelle\"";
		if ( @$_GET['action'] == "edit" and @$_GET['date'] )
		{
			echo "value=\"".$_GET['date']."\"";
		}
		echo "></input></td>";
		echo "<th colspan=4>(format AAAA-MM-JJ HH:MM:SS)</th>";
		echo "<tr>";
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
			echo "<th>".$tableau["lib_champ"]."</th>";
			echo "<td><input type=\"text\" name=\"compteur_$id_compteur\" value=\"".$tableau["derniere_valeur"]."\"></input></td>";
			// Cas spécifique de l'AP
			if ( $id_compteur == 0 )	
			{	
				echo "<td>Niveau $niveau</td>";	
				$tableau["nb_max"] = $tableau_niveau["AP_necessaire"];
			}
			else
			{
/*				if ( @$tableau["url_couleur_medaille"] ) {	echo "<td><img src=\"".$tableau["url_couleur_medaille"]."\" alt=\"".$tableau["couleur_medaille"]."\"></td>";		} */
				echo "<td>&nbsp;</td>";	
			}
			if ( @$tableau["derniere_valeur"] == 0 or @$tableau["nb_max"] == 0)
			{
				$pourcentage = 0;
				echo "<td>&nbsp;</td>";
				echo "<td>&nbsp;</td>";
				if ( @$tableau["url_couleur_medaille"] ) {	echo "<td><img src=\"".$tableau["url_couleur_medaille"]."\" alt=\"".$tableau["couleur_medaille"]."\"></td>";		}				
			}
			else
			{
				$pourcentage=100.0 * $tableau["derniere_valeur"] / $tableau["nb_max"];
				$classe=($pourcentage >= 80 ? "highlight" : "");
				echo "<td class=\"$classe\">".sprintf("%0.2f", $pourcentage)."%</td>";
				echo "<td>".($tableau["derniere_valeur"] - $tableau["nb_max"])."</td>";
				if (! isset($tableau["nb_min"]) ) { $tableau["nb_min"] = 0; }
				$pourcentage_intra_niveau=100.0 * ( $tableau["derniere_valeur"] - $tableau["nb_min"])/($tableau["nb_max"] - $tableau["nb_min"] );
				if ( $id_compteur == 0 )
				{
					echo "<td colspan=3 label=\"progression vers niveau ".($niveau + 1)."\"><div class=\"progressbar niveau\"><div style=\"width: ".sprintf("%0.2f", $pourcentage_intra_niveau)."%\"></div></td>";
				}
				else
				{
					if ( @$tableau["url_couleur_medaille"] ) {	echo "<td><img src=\"".$tableau["url_couleur_medaille"]."\" alt=\"".$tableau["couleur_medaille"]."\"></td>";		}
					else 							{	echo "<td>&nbsp;</td>";		}

					echo "<td alt=\"progression vers medaille ".$tableau["couleur_medaille_suivante"]."\"><div class=\"progressbar ".$tableau["couleur_medaille_suivante"]."\"><div style=\"width: ".sprintf("%0.2f", $pourcentage_intra_niveau)."%\"></div></td>";
					
					//echo "<td><div class=\"progressbar ".$tableau["couleur_medaille_suivante"]."\"><div style=\"width: ".sprintf("%0.2f", $pourcentage_intra_niveau)."%\"></div></td>";
					if ( @$tableau["url_medaille_suivante"] ) {	echo "<td><img src=\"".$tableau["url_medaille_suivante"]."\" alt=\"".$tableau["couleur_medaille_suivante"]."\"></td>";		}
					else 							{	echo "<td>&nbsp;</td>";		}
					
				}
				
			}

			if ( $id_compteur == 0 )
			{
				echo "<td>";
				if ($tableau_niveau["medailles_necessaire"]["nb_ag"] > 0 ) { echo $tableau_niveau["medailles_necessaire"]["nb_ag"]."x<img src=images/silver.png></img>"; }
				if ($tableau_niveau["medailles_necessaire"]["nb_au"] > 0 ) { echo $tableau_niveau["medailles_necessaire"]["nb_au"]."x<img src=images/gold.png></img>"; }
				if ($tableau_niveau["medailles_necessaire"]["nb_pt"] > 0 ) { echo $tableau_niveau["medailles_necessaire"]["nb_pt"]."x<img src=images/platinum.png></img>"; }
				if ($tableau_niveau["medailles_necessaire"]["nb_black"] > 0 ) { echo $tableau_niveau["medailles_necessaire"]["nb_black"]."x<img src=images/black.png></img>"; }
				echo "</td>";
			}
			echo "</tr>\n";
		}
		echo "<tr>";
		echo "<td colspan=4>";
		if ($tableau_niveau["medailles"]["nb_br"] > 0 ) { echo $tableau_niveau["medailles"]["nb_br"]."x<img src=images/bronze.png></img>"; }
		if ($tableau_niveau["medailles"]["nb_ag"] > 0 ) { echo $tableau_niveau["medailles"]["nb_ag"]."x<img src=images/silver.png></img>"; }
		if ($tableau_niveau["medailles"]["nb_au"] > 0 ) { echo $tableau_niveau["medailles"]["nb_au"]."x<img src=images/gold.png></img>"; }
		if ($tableau_niveau["medailles"]["nb_pt"] > 0 ) { echo $tableau_niveau["medailles"]["nb_pt"]."x<img src=images/platinum.png></img>"; }
		if ($tableau_niveau["medailles"]["nb_black"] > 0 ) { echo $tableau_niveau["medailles"]["nb_black"]."x<img src=images/black.png></img>"; }
		echo "</td>";
		echo "</tr>";
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
