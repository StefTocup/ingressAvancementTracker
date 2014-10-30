<?php
	require_once("entete.php");
	require_once("MySQLConnect.php");
	
	$temps = "all";
	$id_guardian = 11;
	if ( @$security["id_joueur"])
	{
		// Définition des bornes d'analyse ( les données seront prises sur la derniere semaine, le dernier mois ou depuis le début )
		switch($temps)
		{
			case "semaine" :
				$sql = "select ";
				break;
			case "mois"    :
				break;
			default        :
				$sql = "select min(date) as min, max(date) as max, max( unix_timestamp( date)) as ts_max, 
						( unix_timestamp( max(date) ) - unix_timestamp( min(date) ) ) / 86400 as delta
						from historique
						where id_joueur = ".$row["id_joueur"];
				break;
		}
		$tableCompteurs = array();
		$res = $mysqli->query( $sql );
		if ( $res->num_rows > 0)
		{
			$row = $res->fetch_assoc();
			// récupération des dates minimum/maximum de notre plage d'analyse
			$date_mini = $row["min"];
			$date_max  = $row["max"];
			$ts_max    = $row["ts_max"];
			// Récupération de la durée de notre plage d'analyse
			$delta     = $row["delta"];
			// Récupération données pour minimum
			$sql = "select id_compteur, valeur from historique where id_joueur = ".$security["id_joueur"]." and date = '".$date_mini."' order by id_compteur";
			$res = $mysqli->query( $sql );
			while ( $row = $res->fetch_assoc()) 
			{
				$tableCompteurs[ $row["id_compteur"] ]["min"] = $row["valeur"];
			}
			
			// Récupération données pour maximum
			$sql = "select id_compteur, valeur from historique where id_joueur = ".$security["id_joueur"]." and date = '".$date_max."' order by id_compteur";
			$res = $mysqli->query( $sql );
			while ( $row = $res->fetch_assoc() ) 
			{
				$tableCompteurs[ $row["id_compteur"] ]["actuel"]   = $row["valeur"];
				// Dans le cas du guardian la pente est forcement de 1
				if ( $row["id_compteur"]  == $id_guardian )	{
					$tableCompteurs[ $row["id_compteur"] ]["pente"] = 1;
				}
				else	{
					// Sinon on calcule la pente de la droite
					$tableCompteurs[ $row["id_compteur"] ]["pente"] = ( $row["valeur"] - $tableCompteurs[ $row["id_compteur"] ]["min"] ) / $delta;
				}
			}
			print "<table>";
			// Lecture de la table des médailles
			// et Affichage des entetes
			$sql = "select cm.url_couleur_medaille, cm.lib_couleur_medaille, cm.id_couleur_medaille
					from couleur_medaille cm
					where id_couleur_medaille > 0
					order by cm.id_couleur_medaille";
			$res = $mysqli->query( $sql );
			$tableauCouleurMedaille = array();
			echo "<tr><td></td>";
			echo "<td></td>";
			while ( $row = $res->fetch_assoc() )
			{
				$tableauCouleurMedaille[ $row["id_couleur_medaille"] ] ["url_couleur_medaille"] = $row["url_couleur_medaille"];
				$tableauCouleurMedaille[ $row["id_couleur_medaille"] ] ["lib_couleur_medaille"] = $row["lib_couleur_medaille"];
				echo "<td><img src=\"".$row["url_couleur_medaille"]."\"/></td>";
			}
			echo "</tr>";
			
			// Lecture des avancements
			$sql = "select c.lib_medaille, c.id_compteur, pm.nb_min, pm.id_couleur_medaille
					from compteurs c, palliers_medailles pm
					where c.id_compteur = pm.id_compteur
					order by pm.id_compteur, pm.id_couleur_medaille";
			$res = $mysqli->query( $sql );
			$old_id_compteur = "";
			// moment où les médailles seront gagneés 
			$gain_medailles = array();
			// Nombre de médailles de base suivant les dernieres informations
			$base_medailles  = array();
			while ( $row = $res->fetch_assoc() )
			{
				if ( $old_id_compteur != $row["id_compteur"] )
				{
					if ( $old_id_compteur != "" )
					{
						echo "</tr>";
					}
					echo "<tr>";
					echo "<td>".$row["lib_medaille"]."</td>";
					echo "<td>".$tableCompteurs[ $row["id_compteur"] ]["actuel"]."</td>";
				}
				
				if ( $tableCompteurs[ $row["id_compteur"] ]["actuel"] > $row["nb_min"] )
				{
					switch ($row["id_couleur_medaille"] )
					{
						case 2 : @$base_medailles[ "nb_ag"] ++;
							break;
						case 3 : @$base_medailles[ "nb_au"] ++;
							break;
						case 4 : @$base_medailles[ "nb_pt"] ++;
							break;
						case 5 : @$base_medailles[ "nb_black"] ++;
							break;
					}

					echo "<td>OK</td>";
				}
				else
				{
					if ( $tableCompteurs[ $row["id_compteur"] ]["pente"] )
					{
						//$champ = $row["nb_min"] - $tableCompteurs[ $row["id_compteur"] ]["actuel"];
						//$champ = $champ / $tableCompteurs[ $row["id_compteur"] ]["pente"];
						$ts = $ts_max;
						$nb_jours = ( $row["nb_min"] - $tableCompteurs[ $row["id_compteur"] ]["actuel"] ) / $tableCompteurs[ $row["id_compteur"] ]["pente"];
						$ts += ( 86400 * $nb_jours );
						switch ($row["id_couleur_medaille"] )
						{
							case 2 : @$gain_medailles[ $nb_jours ] [ "nb_ag"] ++;
								break;
							case 3 : @$gain_medailles[ $nb_jours ] [ "nb_au"] ++;
								break;
							case 4 : @$gain_medailles[ $nb_jours ] [ "nb_pt"] ++;
								break;
							case 5 : @$gain_medailles[ $nb_jours ] [ "nb_black"] ++;
								break;
						}
						$champDate = date( "Y-m-d", $ts);
					}
					else
					{
						$champDate = "N/A";
					}
					
					echo "<td>$champDate</td>";
				}
				$old_id_compteur = $row["id_compteur"];
			}
			echo "</tr>";
			echo "</table>";
			$total_medailles = array();
			
			$sql = "select * from niveaux order by niveau";
			$res = $mysqli->query( $sql );
			ksort ( $gain_medailles );
			echo "<table>";
			print "<td>niveau</td>";
			print "<td>m&eacute;dailles n&eacute;cessaires obtenues dans</td>";
			print "<td>AP n&eacute;cessaires obtenues dans</td>";
			print "<td>Date obtention niveau</td>";
			while ( $row = $res->fetch_assoc() )
			{
				$ap = $tableCompteurs[ 0 ]["actuel"];
				$nb_ap_j = $tableCompteurs[ 0 ]["pente"];
				
				$nb_jours_ap = ($row["AP"] - $ap )/ $nb_ap_j;
				
				$nb_ag    = (@$base_medailles["nb_ag"] ? $base_medailles["nb_ag"] : 0 );
				$nb_au    = (@$base_medailles["nb_au"] ? $base_medailles["nb_au"] : 0 );
				$nb_pt    = (@$base_medailles["nb_pt"] ? $base_medailles["nb_pt"] : 0 );
				$nb_black = (@$base_medailles["nb_black"] ? $base_medailles["nb_black"] : 0 );
				if ( $nb_ag    >= $row["nb_ag"]
					and $nb_au >= $row["nb_au"]
					and $nb_pt >= $row["nb_pt"]
					and $nb_black >= $row["nb_black"] 
					and $ap >= $row["AP"])
				{
					// Le niveau est déjà ok 
				}
				else
				{
					foreach ( $gain_medailles as $nb_jours => $tableau)
					{
						$nb_ag    += @$tableau["nb_ag"];
						$nb_au    += @$tableau["nb_au"];
						$nb_pt    += @$tableau["nb_pt"];
						$nb_black += @$tableau["nb_black"];
						if ( $nb_ag >= $row["nb_ag"]
							and $nb_au >= $row["nb_au"]
							and $nb_pt >= $row["nb_pt"]
							and $nb_black >= $row["nb_black"] )
						{
							print "<tr>";
							print "<td>".$row["niveau"]."</td>";
							print "<td>$nb_jours j</td>";
							print "<td>".round($nb_jours_ap)." j</td>";
							$nb_j_attente = ($nb_jours > $nb_jours_ap ? $nb_jours : $nb_jours_ap);
							print "<td>".date("Y-m-d", $ts_max + $nb_j_attente * 86400 )."</td>";
							print "</tr>";
							break;
						}
					}
				}
			}
			echo "</table>";
		}
		
	}
	
	
	
?>




<?php
	require_once("enqueue.php");
?>
