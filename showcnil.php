
<?php 
include_once("fonctions.php");
if (!isset($_GET['fichier'])){
    echo "<H1>Affichage Fichier</H2><H2>Paramètre showcnil?fichier=xxx non fourni</H2>";
    exit();
}
else $fichier = $_GET['fichier'];
$dir = "./dataxml/cnil/";

if (!file_exists($dir.$fichier)){
        echo "<H1>Affichage Fichier</H2><H2>fichier ($fichier) inconnu</H2>";
        exit();
}

print cnil2html($fichier);
?>
