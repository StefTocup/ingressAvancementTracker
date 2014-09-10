<?php
        $db = "ingress";
        $db_host = "localhost";
        $db_user = "ingress";
        $db_pass = "XXXXXXXX";

		$mysqli = new mysqli( $db_host, $db_user , $db_pass, $db);
		if ($mysqli->connect_errno) {
			echo "Echec lors de la connexion à MySQL  : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
			exit(-1);
		}
        
?>
