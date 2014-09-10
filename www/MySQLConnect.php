<?php
        $db = "ingress";
        $db_host = "localhost";
        $db_user = "ingress";
        $db_pass = "iunsgerress";

        $mysql_cnx = mysql_connect($db_host, $db_user , $db_pass);
        if (!$mysql_cnx)
        {
                echo "Impossible de se connecter a $db<BR>";
                exit (-1);
        }
        mysql_select_db($db);
?>
