<?php
    session_start();
    if (isset($_POST)) $_SESSION['post']=$_POST;
    if (isset($_SESSION['post'])) $_POST=$_SESSION['post'];
    if (isset($_POST['CLEAR'])) {unset($_SESSION); unset($_POST);$_SESSION=array(); $_POST=array();}
    include("fonctions.php");
    include("connexion.php");
    include_once("trace.php");
    // include_once("correction.php");
    // $dateMAJ = "4 septembre 2023";
    $compteur = 0;
    $nbdelibs = 0;
    $nbfichiers = 0;

    // Récupération de la date de la dernière mise à jour depuis la base de données
	$rq_last_update_date = "SELECT date_mise_a_jour FROM MisesAJour ORDER BY id DESC LIMIT 1";
	$rs_last_update_date = mysqli_query($connexion, $rq_last_update_date);
	$row_last_update_date = mysqli_fetch_assoc($rs_last_update_date);
	$last_update_date = $row_last_update_date['date_mise_a_jour'];
	$last_update_date_formatted = date('d/m/Y', strtotime($last_update_date));

	// Vérification si une mise à jour est nécessaire
	if (!$last_update_date || strtotime($last_update_date) < strtotime('-7 days')) {
		// Lancer le script de mise à jour
		include("update_database.php");

		// Mettre à jour la date dans la base de données
		$current_date = date('Y-m-d H:i:s');
		$rq_insert_update_date = "INSERT INTO MisesAJour (date_mise_a_jour) VALUES ('$current_date')";
		mysqli_query($connexion, $rq_insert_update_date);
	}
?>
<html>
    <head>
        <title>DATA CNIL</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="./style/style.css">
    </head>
    <body>
<?php
// print "<H1>les données de travail pour m'en sortir</H1>";
// print "<table width=80% border=3><tr><th width=30%>SESSION</th><th width=30%>POST</th><th width=30%>GET</th></tr>";
// print "<tr><td>";
// var_dump($_SESSION);
// print "</td><td>";
// var_dump($_POST);
// print "</td><td>";
// var_dump($_GET);
// print "</td></tr></table>"
?>
        <H1 align=center><br>DATA CNIL </H1>

        <table width=80% align=center border="thin solid white">
            <tr>
                <td width=50% valign="top">
                    <H2>Etat du système :</H2>
                    <ul>
                        <li>Nombre de fichiers de délibération  : <?php compte_delib_xml(); ?>
                        <li>Nombre de délibérations en BD : <?php compte_delib_bd(); ?>
                        <li>Nombre de "mots" indexés en BD : <?php //compte_token(); ?>
                        <li>La dernière mise à jour de la base de données date du <?php echo $last_update_date_formatted; ?>. </ul>
                        <li>Si entre-temps vous avez remarqué que de nouveaux documents sont apparus sur le site CNIL et que vous souhaitez les explorer instantanément via la plateforme, vous pouvez mettre à jour la base de données en cliquant sur le bouton suivant : </li>
                        <a href='./update_database.php' target='_blank'><button>METTRE A JOUR</button></a>
                    </ul>

<?php
    function compte_delib_xml(){
        global $compteur;
        $size = 0;
        $dir = "./dataxml/cnil/";
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ((endsWith($file, "xml"))&&(startsWith($file, "CNILTEXT"))){
                    $compteur++;
                    $size += filesize($dir."/".$file);
                    //echo "$compteur -- fichier : $file : size : " . filesize($dir . $file) . "<br/>";
                    }
                }
                closedir($dh);
            }
            print "<ul><li>".number_format($compteur,0,",","&nbsp;")." fichiers de délibération";
            print "<li>".number_format($size,0,",","&nbsp;")." octets total";
            print "<li>".number_format($size/$compteur,2,",","&nbsp;")." octets de moyenne</ul>";
        }
    }
    function compte_delib_bd(){
        global $nbfichiers, $nbdelibs, $connexion;
        $rq_nbfichiers = "SELECT count(ID_fichier) AS NB FROM DTC_fichiers_open";
        $rs_nbfichiers = mysqli_query($connexion, $rq_nbfichiers);
        $ro_nbfichiers = $rs_nbfichiers->fetch_object();
        $nbfichiers = $ro_nbfichiers->NB;
        print "<ul><li>".number_format($nbfichiers,0,",","&nbsp;")." fichiers identifiés";
        $rq_nbdelibs = "SELECT count(IDDelib) as NB FROM Deliberation";
        $rs_nbdelibs = mysqli_query($connexion, $rq_nbdelibs);
        $ro_nbdelibs = $rs_nbdelibs->fetch_object();
        $nbdelibs = $ro_nbdelibs->NB;
        print "<li>".number_format($nbdelibs,0,",","&nbsp;")." fichiers parsés</ul>";
    }
    function compte_token(){
        global $connexion, $nbdelibs;
        $rqnbtok = "SELECT Count(IDToken) AS NB FROM Token";
        $rsnbtok = mysqli_query($connexion, $rqnbtok);
        $lgnbtok = mysqli_fetch_row($rsnbtok);
        $nbtok = $lgnbtok[0];
        print "<ul><li>".number_format($nbtok,0,",","&nbsp;")." graphies uniques indexées";
        $rqtot = "SELECT SUM(NbOcc) AS NB FROM Token2Deliberation";
        $rstot = mysqli_query($connexion, $rqtot);
        $lgtot = mysqli_fetch_row($rstot);
        $nbtot = $lgtot[0];
        print "<li>".number_format($nbtot,0,",","&nbsp;")." graphies totales";
        print "<li>Graphies les plus fréquentes :";
        $rqmostfreq = "SELECT IDToken, SUM(NbOcc) AS NB FROM Token2Deliberation GROUP BY IDToken ORDER BY NB DESC LIMIT 0,10";
        $rsmostfreq = mysqli_query($connexion, $rqmostfreq);
        while ($lgmostfreq = mysqli_fetch_row($rsmostfreq)){
            $idmostfreq = $lgmostfreq[0];
            $nbmostfreq = $lgmostfreq[1];
            $rqgraphie = "SELECT Graphie FROM Token WHERE IDToken=$idmostfreq";
            $rsgraphie = mysqli_query($connexion, $rqgraphie);
            $lggraphie = mysqli_fetch_row($rsgraphie);
            $graphie = $lggraphie[0];
            print " $graphie&nbsp;(".number_format($nbmostfreq,0,",","&nbsp;").") ";
        }
        $nbhapax = "SELECT IDToken, SUM(NbOcc) AS NB FROM Token2Deliberation GROUP BY IDToken HAVING NB=1 ";
        $rshapax = mysqli_query($connexion, $nbhapax);
        $nbhapax = mysqli_num_rows($rshapax);
        print "<li>".number_format($nbhapax,"0",",","&nbsp;")." hapaxes";
        $rqlastid = "SELECT IDDelib FROM Token2Deliberation ORDER BY IDDelib DESC LIMIT 0,1";
        $rslastid = mysqli_query($connexion, $rqlastid);
        $lglastid = mysqli_fetch_row($rslastid);
        $lastid = $lglastid[0];
        print "<li>Volume traité : ".number_format($lastid*100/$nbdelibs,2,",","&nbsp;")."% (ID : $lastid)";
        print "</ul>";
    }
print "<ul>";
$pct1 = round($nbfichiers*100/$compteur,2);
$pct2 = round($nbdelibs*100/$compteur,2);
if ($nbfichiers<$compteur) print "<li>Veuillez <a href=\"parcourir.php\">parcourir</a> l'arboressence pour mettre à jour ($pct1 %)";
if ($nbdelibs<$compteur) print "<li>Veuillez <a href=\"parser.php\">parser</a> les fichiers XML pour mettre à jour ($pct2 %)";
    $rqlastid = "SELECT IDDelib FROM Token2Deliberation ORDER BY IDDelib DESC LIMIT 0,1";
    $rslastid = mysqli_query($connexion, $rqlastid);
    $lglastid = mysqli_fetch_row($rslastid);
    $lastidparsed = $lglastid[0];
    $rqlastid = "SELECT IDDelib FROM Deliberation ORDER BY IDDelib DESC LIMIT 0,1";
    $rslastid = mysqli_query($connexion, $rqlastid);
    $lglastid = mysqli_fetch_row($rslastid);
    $lastidstored = $lglastid[0];
    if ($lastidparsed<$lastidstored) print "<li><a href=\"makelexique.php?first=".($lastidparsed+1)."\">Lancer le traitement lexical</a> des ".($lastidstored-$lastidparsed)." délibérations restantes";

print "</ul>"
?>
                </td>
            <form method="post" action="index.php">
                <td width=50% valign="top">
                    <H2>Restrictions</H2>
                        <label>Nature de la délibération : </label>
                        <div style="height: 150px; overflow: auto; background-color : rgb(251, 191, 191);">
                        <!-- <div style="height: 150px; overflow: auto; background-color : #ecf9d8;"> -->
                            <?php
                            $rqtypedec = "SELECT NatureDeliberation, count(IDDelib) FROM Deliberation GROUP BY NatureDeliberation ORDER BY NatureDeliberation";
                            $rstypedec = mysqli_query($connexion, $rqtypedec);
                            while ($lgdec = mysqli_fetch_row($rstypedec)) {
                                if (isset($_POST[str_replace(array(" ",".","&gt;"),array("_","_",">"),$lgdec[0])])) $checked="CHECKED"; else $checked="";
                                echo "<input type=\"checkbox\" name=\"".$lgdec[0]."\" value=\"".$lgdec[0]."\" $checked>".$lgdec[0]." (".number_format($lgdec[1],0,",","&nbsp;").")<br/>";
                            }
                            
                            ?>
                        </div>
                    
                        <label>Nature du document : </label>
                        <select name="nature">
                            <?php if(isset($_POST['nature'])) echo "<option value=\"".$_POST['nature']."\">".$_POST['nature']; ?>
                            <option value="">
                            <?php
                                $rqnatdec = "SELECT NatureDocument, count(IDDelib) FROM Deliberation GROUP BY NatureDocument ORDER BY NatureDocument";
                                $rsnatdec = mysqli_query($connexion, $rqnatdec);
                                while ($lgdec=mysqli_fetch_row($rsnatdec)) echo "<option value=\"".$lgdec[0]."\">".$lgdec[0]." (".$lgdec[1].")";
                            ?>
                        </select><br/>
                        <label>Entre dates :</label>
                            <input type=text name="datedebut" value="<?php if(isset($_POST['datedebut'])) echo date2fr($_POST['datedebut']); else echo "JJ/MM/AAAA";?>">
                            <input type=text name="datefin" value="<?php if(isset($_POST['datefin'])) echo date2fr($_POST['datefin']); else echo "JJ/MM/AAAA";?>">
                            <br/>Affichage par <input type=radio name="cycle" value="mois" <?php if (isset($_POST['cycle'])) {if ($_POST['cycle']=="mois") echo "CHECKED";}?>>mois 
                            ou par <input type=radio name="cycle" value="annee" <?php if ((!isset($_POST['cycle']))||($_POST['cycle']!="mois")) echo "CHECKED";?>>année<br/>
                        
                        <label>Titre/Texte contiennent :</label>							
						    <input type=text name="titreOuTexteContiennent" value="<?php if(isset($_POST['titreOuTexteContiennent'])) echo stripslashes($_POST['titreOuTexteContiennent']); ?>"><br/>
  						<label>Titre/Texte ne contiennent pas :</label>							
						    <input type=text name="titreOuTexteNeContiennentPas" value="<?php if(isset($_POST['titreOuTexteNeContiennentPas'])) echo stripslashes($_POST['titreOuTexteNeContiennentPas']); ?>"><br/>
                          

                            <label>Titre contient :</label>
                            <input type=text name="titrecontient" value="<?php if(isset($_POST['titrecontient'])) echo stripslashes($_POST['titrecontient']); ?>"><br/>
                        <label>Titre ne contient pas :</label>
                            <input type=text name="titrenecontientpas" value="<?php if(isset($_POST['titrenecontientpas'])) echo stripslashes($_POST['titrenecontientpas']); ?>"><br/>
                        <label>Texte contient :</label>
                            <input type=text name="textecontient" value="<?php if(isset($_POST['textecontient'])) echo stripslashes($_POST['textecontient']); ?>"><br/>
                        <label>Texte ne contient pas :</label>
                            <input type=text name="textenecontientpas" value="<?php if(isset($_POST['textenecontientpas'])) echo stripslashes($_POST['textenecontientpas']); ?>"><br/>
                        <br/>
                        <CENTER><input type="submit" name="FILTRER" value="FILTRER LES TEXTES">
                        </form>
                        <form method="POST"><input type="submit" name="CLEAR" value="Effacer le filtre"></form>
                        </CENTER>

                </td>
            </tr>
        </table>

<?php
$listeannee="";
$listevaleurs="";
$nbtotal = 0;
$styleText = "none";
if (!isset($_POST['FILTRER'])){
    $rqnb = "SELECT Count(IDDelib), AnneeTexte FROM Deliberation WHERE AnneeTexte NOT LIKE \"2999\" GROUP BY AnneeTexte ORDER BY AnneeTexte";
    $rsnb = mysqli_query($connexion, $rqnb);
    while($lgnb=mysqli_fetch_row($rsnb)){
        $listeannee .= "'".$lgnb[1]."', ";
        $listevaleurs .="'".$lgnb[0]."', ";
        $nbtotal += $lgnb[0];        

    }
}
else {
	$styleText = "inherit";
    //construction de la requête
    $contraintes=" 1 ";
        // contrainte sur la nature de la délibération
        $contraintedelib ="0";
        $rqtypedec = "SELECT NatureDeliberation, count(IDDelib) FROM Deliberation GROUP BY NatureDeliberation ORDER BY NatureDeliberation";
        $rstypedec = mysqli_query($connexion, $rqtypedec);
        while ($lgdec = mysqli_fetch_row($rstypedec)) {
            $typedec = str_replace(array(" ",".","&gt;"),array("_","_",">"),$lgdec[0]);
            if (isset($_POST[$typedec])) $contraintedelib.=" OR NatureDeliberation LIKE \"".$lgdec[0]."\"";
        }
        if ($contraintedelib!="0") $contraintes .= "AND ($contraintedelib)";
        //if (trim($_POST['typedec'])!=""){$contraintes.=" AND NatureDeliberation LIKE \"".$_POST['typedec']."\" ";}
        // contrainte sur la nature du document
        if (trim($_POST['nature'])!=""){$contraintes.= " AND NatureDocument LIKE \"".$_POST['nature']."\" ";}
        // contrainte sur la date de début
        if (trim($_POST['datedebut'])!="JJ/MM/AAAA") {$contraintes.=" AND DateTexte>=\"".date2en(trim($_POST['datedebut']))."\" ";}
        // contrainte sur la date de fin
        if (trim($_POST['datefin'])!="JJ/MM/AAAA") {$contraintes.=" AND DateTexte<=\"".date2en(trim($_POST['datefin']))."\" ";}
        else {$contraintes.=" AND DateTexte < \"2999/01/01\"";}
		
				
	

		// Function to retrieve dictionary words from the database
		function getDictionaryWords($conn) {
			$words = array();
			$sql = "SELECT Graphie FROM Token";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) {
					$words[] = $row['Graphie'];
				}
			}
			return $words;
		}
		
								
								
		// Fonction permettant de déterminer l'opérateur approprié
        // Utiliser % comme joker
		function getOperator($keywords){
			if (strpos($keywords, ' OR ') !== false) {
				return ' OR ';
			} elseif (strpos($keywords, ',') !== false) {
				return ' AND ';
			} else {
				return ' AND ';
			}
		}

        // contrainte sur contenu du titre ou du texte
		// if ($_POST['titreOuTexteContiennent']) {
		if (isset($_POST['titreOuTexteContiennent']) && !empty($_POST['titreOuTexteContiennent'])) {
			$keywords = $_POST['titreOuTexteContiennent'];
			$operator = getOperator($keywords);
			$tabtok = preg_split('/(?:\s*,\s*|\s+AND\s+|\s+OR\s+)/i', $keywords);
			$conditions = array();
			foreach($tabtok as $tok){ 
				$tok=trim(stripslashes($tok)); 
				$conditions[] = "(TitreLong LIKE \"%$tok%\" OR Contenu LIKE \"%$tok%\")";
			}
			$contraintes .= " AND (" . implode($operator, $conditions) . ")";
		}

		// contrainte sur non contenu du titre ou du texte
		if (isset($_POST['titreOuTexteNeContiennentPas']) && !empty($_POST['titreOuTexteNeContiennentPas'])) {

			$keywords = $_POST['titreOuTexteNeContiennentPas'];
			$operator = getOperator($keywords);
			$tabtok = preg_split('/(?:\s*,\s*|\s+AND\s+|\s+OR\s+)/i', $keywords);
			$conditions = array();
			foreach($tabtok as $tok){ 
				$tok=trim(stripslashes($tok)); 
				$conditions[] = "(TitreLong NOT LIKE \"%$tok%\" AND Contenu NOT LIKE \"%$tok%\")";
			}
			$contraintes .= " AND (" . implode($operator, $conditions) . ")";
		}



		// contrainte sur contenu du titre
		if ($_POST['titrecontient']){
			$keywords = $_POST['titrecontient'];
			$operator = getOperator($keywords);
			$tabtok = preg_split('/(?:\s*,\s*|\s+AND\s+|\s+OR\s+)/i', $keywords);
			$conditions = array();
			foreach($tabtok as $tok){ 
				$tok=trim(stripslashes($tok)); 
				$conditions[] = "TitreLong LIKE \"%$tok%\"";
			}
			$contraintes .= " AND (" . implode($operator, $conditions) . ")";
		}

		// contrainte sur non contenu du titre
		if ($_POST['titrenecontientpas']){
			$keywords = $_POST['titrenecontientpas'];
			$operator = getOperator($keywords);
			$tabtok = preg_split('/(?:\s*,\s*|\s+AND\s+|\s+OR\s+)/i', $keywords);
			$conditions = array();
			foreach($tabtok as $tok){ 
				$tok=trim(stripslashes($tok)); 
				$conditions[] = "TitreLong NOT LIKE \"%$tok%\"";
			}
			$contraintes .= " AND (" . implode($operator, $conditions) . ")";
		}

		// contrainte sur contenu du texte
		if ($_POST['textecontient']){
			$keywords = $_POST['textecontient'];
			$operator = getOperator($keywords);
			$tabtok = preg_split('/(?:\s*,\s*|\s+AND\s+|\s+OR\s+)/i', $keywords);
			$conditions = array();
			foreach($tabtok as $tok){ 
				$tok=trim(stripslashes($tok)); 
				$conditions[] = "Contenu LIKE \"%$tok%\"";
			}
			$contraintes .= " AND (" . implode($operator, $conditions) . ")";
		}

		// contrainte sur non contenu du texte
		if ($_POST['textenecontientpas']){
			$keywords = $_POST['textenecontientpas'];
			$operator = getOperator($keywords);
			$tabtok = preg_split('/(?:\s*,\s*|\s+AND\s+|\s+OR\s+)/i', $keywords);
			$conditions = array();
			foreach($tabtok as $tok){ 
				$tok=trim(stripslashes($tok)); 
				$conditions[] = "Contenu NOT LIKE \"%$tok%\"";
			}
			$contraintes .= " AND (" . implode($operator, $conditions) . ")";
		}

			
			
        // gestion du cycle d'affichage
        if ($_POST['cycle']=="mois") $by=" AnneeTexte, MoisTexte";
            else $by = " AnneeTexte";
        // construction de la requete complexer
        $_SESSION['contraintes']=$contraintes;
        $rqnb = "SELECT Count(IDDelib), $by FROM Deliberation WHERE $contraintes GROUP BY $by ORDER BY $by";
        // print "<br/><span style=\"font-size:9pt;\">Requête SQL : [$rqnb]
        //         <br/>contraintes (".strlen($contraintes).") : $contraintes</span>";
        $rsnb = mysqli_query($connexion, $rqnb);
        while($lgnb=mysqli_fetch_row($rsnb)){
            if ($_POST['cycle']=="mois"){
                $listeannee .= "'".$lgnb[2]."/".$lgnb[1]."', ";
                $listevaleurs .="'".$lgnb[0]."', "; 
                $nbtotal += $lgnb[0];        
            }
            else {
                $listeannee .= "'".$lgnb[1]."', ";
                $listevaleurs .="'".$lgnb[0]."', ";
                $nbtotal += $lgnb[0];                
            }
    }
}
$listeannee=substr($listeannee,0, strlen($listeannee)-2);
$listevaleurs=substr($listevaleurs,0, strlen($listevaleurs)-2);

    print "<table width=\"80%\" align=\"center\"><tr>";
    if ((isset($_POST['textecontient']))&&($_POST['textecontient']!="")&& (!strpos($_POST['textecontient'],","))){
        print "<th width=\"50%\"><span style=\"line-height:50px;\">
        <a href=\"concord.php\" target=\"new\"><img src=\"images/new_concordancier1.png\" width=\"170px\" style=\"float:left;\"></a>
        <a href=\"concord.php?\" download><img src=\"images/new_concordancier2.png\" width=\"160px\" style=\"float:center;\"></a>
        </span></th>\n";
    } else print "<th width=\"50%\">&nbsp;</th>";
    // print "<th width=\"50%\"><span style=\"line-height:50px;\"> Voir <a href=\"loaddelibs.php?ref=all\" target=\"new\">l'ensemble des délibérations<img src=\"./images/new_folder1.png\" width=\"50px\" style=\"float:left;\"><img src=\"images/folder.png\" width=\"50px\" style=\"float:right;\"></a></span></th>";
    print "<th width=\"50%\"><span style=\"line-height:50px;\">
    <a href=\"loaddelibs.php?ref=all\" target=\"new\"><img src=\"./images/new_folder1.png\" width=\"160px\" style=\"float:center;\"></a>
    <a href=\"loaddelibs.php?ref=all\" download><img src=\"./images/new_folder2.png\" width=\"160px\" style=\"float:right;\"></a>
    </span></th>";
    
    print "</tr></table>";
    print"<br><br>";

?>

<canvas id="myChart" width="400" height="100"></canvas>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

<script>

var ctx = document.getElementById('myChart').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {

        labels: [<?php echo $listeannee; ?>],
        datasets: [{
            label: 'nombre de délibérations (<?php echo number_format($nbtotal,0,","," ");?>)',
            data: [<?php echo $listevaleurs; ?>],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

document.getElementById("myChart").onclick = function(evt){
    var activePoints = myChart.getElementsAtEventForMode(evt,'nearest', {intersect: true}, true);
	var firstPoint = activePoints[0];
	var label = myChart.data.labels[firstPoint.index];
	var value = myChart.data.datasets[firstPoint.datasetIndex].data[firstPoint.index];
	if (firstPoint !== undefined){
        //var lesdelibs = document.getElementById('lesdelibs');
		 //lesdelibs.load('loaddelibs.php?ref='+value);
         //alert(label + ": " + value);
         window.open("loaddelibs.php?ref="+label);
    }
};

var myButton = document.getElementById("myButton");
myButton.onclick = function() {
    var activePoints = myChart.getElementsAtEventForMode(evt, 'nearest', {intersect: true}, true);
    var firstPoint = activePoints[0];

    if (firstPoint !== undefined) {
        var label = myChart.data.labels[firstPoint.index];
        window.open("wordcloud.php?ref=" + label);
    }
};


</script>
</canvas>

<?php
    include("avertissement.php");
    print "<div id=\"lesdelibs\">";
    print "<h2>une délibération au hasard parmi les ".number_format($nbdelibs,0,",","&nbsp;")."</h2>";
    $iddel = rand(1,$nbdelibs);
    $rqfichier = "SELECT NomFichier FROM Deliberation WHERE IDFichier=$iddel";
    $rsfichier = mysqli_query($connexion, $rqfichier);
    $lgfichier = mysqli_fetch_row($rsfichier);
    $fichier = $lgfichier[0];
    print cnil2html($fichier);
    print "</div>";
?>
    </body>
</html>
