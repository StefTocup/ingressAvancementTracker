<?php
	require_once("entete.php");
	require_once("MySQLConnect.php");
?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="js/highcharts.js"></script>
<script src="js/modules/exporting.js"></script>
<?php
if ( @$security["login"] != "" )
{
?>
 <div id="container"></div>
    <script>
	Number.prototype.formatNumber=function(c,d,t)
	{
		var n = this;
		var c = (isNaN(c=Math.abs(c))?2:c)
		var d = (d==undefined?".":d)
		var t = (t==undefined?",":t)
		var s = (n < 0 ? "-" : "")
		var i = parseInt(n=Math.abs(+n||0).toFixed(c))+""
		var j = (j=i.length)>3?j%3:0;
		return s+(j?i.substr(0,j)+t:"")+i.substr(j).replace(/(\d{3})(?=\d)/g,"$1"+t)+(c?d+Math.abs(n-i).toFixed(c).slice(2):"");
	}
    var mycolors = new Array('aqua', 'fuchsia', 'gray', 'green', 'lime', 'maroon', 'olive', 'orange', 'purple', 'red', 'silver', 'teal', 'yellow', 'blue', 'white', 'black', 'navy');
    $(function () {
        $('#container').highcharts({
            chart: {
                type: 'spline',
                //scroll problem
                //zoomType: 'x',
                backgroundColor: '#000000',
                style: {
                    fontFamily: '"Coda", "Cursive"',
                },
            },
            colors: mycolors,
            title: {
                text: 'Ingress stats',
                style: {
                    color: '#fdd73c',
                },
            },
            subtitle: {
                text: '@<?php echo @$security["login"] ?>',
                style: {
                    color: '#fdd73c',
                    textTransform: 'none',
                },
            },
            xAxis: {
                type: 'datetime',
                dateTimeLabelFormats: {
                    month: '%e. %b',
                    year: '%b',
                },
                title: {
                    text: null,
                },
            },
            yAxis: [
                {
                    id: 'AP',
                    showEmpty: false,
                    floor: 0,
                    labels: {
                        style: {
                            'color': mycolors[0],
                        },
                    },
                    title: {
                        text: 'AP',
                        style: {
                            'color': mycolors[0],
                            'fontWeight': 'bold',
                        },
                    },
                    plotLines: [
<?php
	$sql = "select niveau, AP from niveaux order by niveau";
	$res = $mysqli->query( $sql );
	while ( $row = $res->fetch_assoc() )
	{
?>
                        { color: mycolors[0], width: 2, value: <?php echo $row["AP"] ?>, dashStyle: 'longdash', label: { text: 'ap level <?php echo $row["niveau"] ?>', style: { color: mycolors[0], }, }, },
<?php
	}
?>
                    ],
                },
<?php
	$sql = "select lib_couleur_medaille, nb_min, lib_medaille, c.id_compteur
			from compteurs c, palliers_medailles pm, couleur_medaille cm
			where c.id_compteur = pm.id_compteur
			and   pm.id_couleur_medaille = cm.id_couleur_medaille
			order by pm.id_compteur, cm.id_couleur_medaille";
	$res = $mysqli->query( $sql );
	$old_medaille = "";
	while ( $row = $res->fetch_assoc() )
	{
		if ( $old_medaille != $row["lib_medaille"] )
		{
			if ( $old_medaille != "" )
			{
				echo " ],\n },\n";
			}
?>
                { id: '<?php echo $row["lib_medaille"] ?>', showEmpty: false, floor: 0,labels: { style: { 'color': mycolors[<?php echo $row["id_compteur"] ?>], }, },
                    title: { text: '<?php echo $row["lib_medaille"] ?>', style: { 'color': mycolors[1],'fontWeight': 'bold', }, },
                    plotLines: [
<?php
		}
?>
                        { color: mycolors[<?php echo $row["id_compteur"] ?>], width: 2, value: <?php echo $row["nb_min"] ?>, dashStyle: 'longdash', label: { text: '<?php echo $row["lib_medaille"] ?> <?php echo $row["lib_couleur_medaille"] ?>', style: { color: mycolors[<?php echo $row["id_compteur"] ?>], }, }, },
<?php
	$old_medaille = $row["lib_medaille"];
	}
?>
                    ],
                },
            ],
            tooltip: {
                formatter: function () {
                    var s = '<b>' + Highcharts.dateFormat('%A, %B %e, %H:%M', new Date(this.x)) + '</b>';
                    $.each(this.points, function () {
                        s += '<br/><span style="color:' + this.series.color + ';">' + this.series.name + ': ' + (this.y).formatNumber(0) +' (+' + (this.point.progress).formatNumber(0) + ')</span>';
                    });
                    return s;
                },
                shared: true,
                borderColor: '#fdd73c',
                borderRadius: 0,
                backgroundColor: '#222222',
                style: {
                    color: '#fdd73c',
                }
            },
            series: [
<?php
$sql = "select c.id_compteur, lib_medaille, UNIX_TIMESTAMP(date)*1000 as ts, valeur 
		from historique h, compteurs c
		where h.id_joueur = ".$security["id_joueur"]."
		and c.id_compteur = h.id_compteur
		and c.id_compteur < 12
		order by c.id_compteur, ts";
$old_medaille = "";
$res = $mysqli->query( $sql );
$old_valeur=0;
while ( $row = $res->fetch_assoc() )
{
	if ( $old_medaille != $row["lib_medaille"] )
	{
		if ( $old_medaille != "" )
		{
echo "                    ],
                }, \n";

		}
?>
                { visible: false,
                    name: '<?php echo $row["lib_medaille"]."(@".$security["login"].")" ?>)',
                    yAxis: '<?php echo $row["lib_medaille"] ?>',
                    color: mycolors[<?php echo $row["id_compteur"] ?>],
                    data: [
<?php
	}
?>
	{x: <?php echo $row["ts"] ?>, y:<?php echo $row["valeur"] ?>, progress:<?php echo ($row["valeur"] - $old_valeur);?>},
<?php
$old_medaille = $row["lib_medaille"] ;
$old_valeur   = $row["valeur"];
}
?>
            ],
        }],
        });
    });
    </script>
<?php
// end if ( @$security["login"] != "" )
}
	require_once("enqueue.php");
?>
