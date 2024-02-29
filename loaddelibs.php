<?php
    session_start();
    if (isset($_SESSION['post'])) $_POST = $_SESSION['post'];
    include_once("trace.php");
?>

<html>
    <head>
        <title>DATA CNIL Selection</title>
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

if(!isset($_GET['ref'])) {echo "Pas de paramètre, affichage impossible"; exit(0);}

$ref = $_GET['ref'];
if ($ref=="all") {$contraintetemp="1";}
else if (strlen($ref)>4){$mois = substr($ref,0,2); $annee= substr($ref,3,4); $contraintetemp = "AnneeTexte LIKE \"$annee\" AND MoisTexte LIKE \"$mois\""; }
else $contraintetemp = "AnneeTexte LIKE \"$ref\"";
include_once("connexion.php");
include_once("fonctions.php");

//affichage initial 
print "<H1>DATA CNIL </H1>\n<H2>Résumé : affichage d'une sélection de décisions\n";
//print "<br/>Rappel requête : <input type=\"texte\" size=80 value=\"".str_replace("\"", "", $_SESSION['contraintes'])."\" />";
$textecontraintes ="<p>Sur la période $ref\n<ul>\n";
$rqtypedec = "SELECT NatureDeliberation, count(IDDelib) FROM Deliberation GROUP BY NatureDeliberation ORDER BY NatureDeliberation";
$rstypedec = mysqli_query($connexion, $rqtypedec);
while ($lgdec = mysqli_fetch_row($rstypedec)) {
    $typedec = str_replace(array(" ",".","&gt;"),array("_","_",">"),$lgdec[0]);
    if (isset($_POST[$typedec])) $textecontraintes.="<li>Nature de délibération : \"".$lgdec[0]."\"";
    }
//if (strlen($_POST['typedec'])>0) $textecontraintes .="<li>Type de décision : ".$_POST['typedec'];
if (strlen($_POST['nature'])>0) $textecontraintes .="<li>Nature du document : ".$_POST['nature'];
if (strlen($_POST['titrecontient'])>0) $textecontraintes .="<li>Le titre long contient : ".$_POST['titrecontient'];
if (strlen($_POST['titrenecontientpas'])>0) $textecontraintes .="<li>Le titre long NE contient PAS : ".$_POST['titrenecontientpas'];
if (strlen($_POST['textecontient'])>0) $textecontraintes .="<li>Le texte contient : ".$_POST['textecontient'];
if (strlen($_POST['textenecontientpas'])>0) $textecontraintes .="<li>Le texte NE contient PAS : ".$_POST['textenecontientpas'];
if (strlen($_POST['titreOuTexteContiennent'])>0) $textecontraintes .="<li>Le Titre ou texte contiennent : ".$_POST['titreOuTexteContiennent'];
if (strlen($_POST['titreOuTexteNeContiennentPas'])>0) $textecontraintes .="<li>Le Titre ou texte NE contiennent PAS : ".$_POST['titreOuTexteNeContiennentPas'];
$textecontraintes.="</ul></p>";
print $textecontraintes;

$rqnb = "SELECT count(IDDelib) FROM Deliberation WHERE ".$_SESSION['contraintes']." AND $contraintetemp  ORDER BY DateTexte";
$rsnb = mysqli_query($connexion, $rqnb);
$lgnb = mysqli_fetch_row($rsnb);
print "<br/>".number_format($lgnb[0],0,",","&nbsp;")." délibérations répondent à ce critère ";
print "<br/><br/>Outils TAL:    <a href=\"wordcloud.php?ref=all\" target=\"new\"><button id=\"myButton\" type=\"button\" class=\"btn btn-outline-primary\">Word Cloud</button></a>
        <a href=\"classificateur.php?ref=all\" target=\"new\"><button id=\"myButton\" type=\"button\" class=\"btn btn-outline-primary\">Classificateur</button></a></H2>";
print"<br/>";
print"<br/>";
print "<a href=\"loaddelibs.php?ref=$ref\" download><button>Exporter les résultats</button></a>";

$rqid = "SELECT NomFichier, DateTexte FROM Deliberation WHERE ".$_SESSION['contraintes']." AND $contraintetemp  ORDER BY DateTexte";
//print "<br/>RQ : $rqid<br/>";
$rsid = mysqli_query($connexion,$rqid);
while ($lgid = mysqli_fetch_row($rsid)){
    //print "<br/>ID : ".$lgid[0]." @ ".$lgid[1];
    print cnil2html($lgid[0])."\n\n<br/><br/><br/><br/>\n\n";

}
?>