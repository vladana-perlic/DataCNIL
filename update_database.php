<?php
include 'connexion.php'; 


// Commande pour exécuter le script Python
$commande = 'python3 download.py';
// Exécuter la commande pour exécuter le script Python
$sortie = shell_exec($commande);

// Afficher la sortie de l'exécution du script Python
echo "<pre>$sortie</pre>";


// Vérifier si le mot "Telechargement" est présent dans la sortie
if (strpos($sortie, 'Telechargement') !== false) {
    // Commande pour supprimer le répertoire 'extracted' ainsi que tous les répertoires à l'intérieur de ./dataxml/cnil/
    $commande_suppression = 'rm -rf extracted';
    $commande_suppression .= ' && find ./dataxml/cnil/ -mindepth 1 -type d -exec rm -rf {} +';

    // Exécuter la commande pour supprimer le répertoire 'extracted'
    $sortie_suppression = shell_exec($commande_suppression);

    // Afficher la sortie de la commande de suppression
    echo "<pre>$sortie_suppression</pre>";

    // Commande pour exécuter le script parcourir.php
    $commande_parcourir = 'php parcourir.php';

    // Exécuter la commande pour exécuter le script parcourir.php
    $sortie_parcourir = shell_exec($commande_parcourir);

    echo "<pre>$sortie_parcourir</pre>";

    // Commande pour exécuter le script parser.php
    $commande_parser = 'php parser.php';

    // Exécuter la commande pour exécuter le script parser.php
    $sortie_parser = shell_exec($commande_parser);

    echo "<pre>$sortie_parser</pre>";
	
	$rqlastid = "SELECT IDDelib FROM Token2Deliberation ORDER BY IDDelib DESC LIMIT 0,1";
	$rslastid = mysqli_query($connexion, $rqlastid);
	$lglastid = mysqli_fetch_row($rslastid);
	$lastidparsed = $lglastid[0];
	$rqlastid = "SELECT IDDelib FROM Deliberation ORDER BY IDDelib DESC LIMIT 0,1";
	$rslastid = mysqli_query($connexion, $rqlastid);
	$lglastid = mysqli_fetch_row($rslastid);
	$lastidstored = $lglastid[0];
	
	
	// Commande pour exécuter le script makelexique.php
	$lastidparsed = $lastidparsed + 1;
    $commande_makelexique = "php makelexique.php?first=$lastidparsed";
    $sortie_makelexique = shell_exec($commande_makelexique);
    echo "<pre>$sortie_makelexique</pre>";


	
} else {
    // Si "Telechargement" n'est pas trouvé
    echo "Pas de nouveaux fichiers.";
}


?>

