<?php
    session_start();
    if (isset($_SESSION['post'])) $_POST = $_SESSION['post'];
    include_once("trace.php");
?>

<html>
    <head>
        <title>DATA CNIL Concordancier</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="style/style.css">
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
// print "</td></tr></table>";

include_once("connexion.php");
include_once("fonctions.php");

//affichage initial 
print "<H1>DATA CNIL Concordancier</H1>\n<H2>Résumé : affichage d'une sélection de décisions\n";
//print "<br/>Rappel requête : <input type=\"texte\" size=80 value=\"".str_replace("\"", "", $_SESSION['contraintes'])."\" />";
$textecontraintes ="<ul>\n";
if (strlen($_POST['nature'])>0) $textecontraintes .="<li>Nature de délibération : ".$_POST['nature'];
$rqtypedec = "SELECT NatureDeliberation, count(IDDelib) FROM Deliberation GROUP BY NatureDeliberation ORDER BY NatureDeliberation";
$rstypedec = mysqli_query($connexion, $rqtypedec);
while ($lgdec = mysqli_fetch_row($rstypedec)) {
    $typedec = str_replace(array(" ",".","&gt;"),array("_","_",">"),$lgdec[0]);
    if (isset($_POST[$typedec])) $textecontraintes.="<li>Nature de délibération : \"".$lgdec[0]."\"";
    }
//if (strlen($_POST['nature'])>0) $textecontraintes .="<li>Nature du document : ".$_POST['nature'];
if (strlen($_POST['titrecontient'])>0) $textecontraintes .="<li>Le titre long contient : ".$_POST['titrecontient'];
if (strlen($_POST['titrenecontientpas'])>0) $textecontraintes .="<li>Le titre long NE contient PAS : ".$_POST['titrenecontientpas'];
if (strlen($_POST['textecontient'])>0) $textecontraintes .="<li>Le texte contient : ".$_POST['textecontient'];
if (strlen($_POST['textenecontientpas'])>0) $textecontraintes .="<li>Le texte NE contient PAS : ".$_POST['textenecontientpas'];
$textecontraintes.="</ul>";
print $textecontraintes;

$rqnb = "SELECT count(IDDelib) FROM Deliberation WHERE ".$_SESSION['contraintes']." ORDER BY DateTexte";
$rsnb = mysqli_query($connexion, $rqnb);
$lgnb = mysqli_fetch_row($rsnb);
print "<br/>".$lgnb[0]." délibérations répondent à ce critère </H2>";

// lien showcnil.php?fichier=$fichier
print "<table width=\"80%\">
        <tr><th align=\"right\">ID</th>
            <th>date</th>
            <th align=\"right\">contexte gauche</th>
            <th align=\"center\">".$_POST['textecontient']."</th>
            <th align=\"left\">contexte droite</th>
        </tr>";
$rqconc = "SELECT IDDelib, NomFichier, Contenu, DateTexte FROM Deliberation WHERE ".$_SESSION['contraintes']." ORDER BY DateTexte";
$find = "/(\s.{0,75})(".trim($_POST['textecontient']).")(.{0,75}\s)/i";
// print "<br>[$rqconc]";
//print "<br>$find";
$rsconc = mysqli_query($connexion, $rqconc);
$prev_id = 0;
while($lgconc = mysqli_fetch_row($rsconc)){
    $iddelib = $lgconc[0];
    $fichier = $lgconc[1];
    $contenu = $lgconc[2];
    $datetexte = $lgconc[3];
    //print "<br/> $datetexte";
    $datetexte= date2fr($datetexte);
    //print " =FR> $datetexte";
    preg_match_all($find, $lgconc[2], $matches, PREG_SET_ORDER);
    foreach($matches AS $match){
        $cg = $match[1];
        $tok = $match[2];
        $cd = $match[3];
        //print "<tr><td>";print_r($match);print"</td></tr>";
        if ($prev_id == $iddelib)
        print "<tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align=right>$cg</td>
                    <td align=center style=\"color:red;\">$tok</td>
                    <td>$cd</td>";                    
        else
        print "<tr>
                    <td align=\"right\"><a href=\"showcnil.php?fichier=$fichier\" target=\"new2\">$iddelib</a></td>
                    <td>$datetexte</td>
                    <td align=right>$cg</td>
                    <td align=center style=\"color:red;\">$tok</td>
                    <td>$cd</td>";                    
        print "</tr>";
    $prev_id = $iddelib;
    }
    print "<tr><td colspan=5>&nbsp;</td></tr>";
}
print "</table>";



?>

    </body>
</html>