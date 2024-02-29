<html>
    <head>
        <title>DATA CNIL / parcours fichiers</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="style/style.css">
    </head>
    <body>
    <H1>DATA CNIL / parcours fichiers</H1>

<?php
    include("fonctions.php");
    include("connexion.php");
    $dir = "./dataxml/cnil/";
    print "<ul>\n";
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if ((endsWith($file, "xml"))&&(startsWith($file, "CNILTEXT"))){
                    //vérifier présent / absent dans BD
                    $rq = "SELECT * FROM DTC_fichiers_open WHERE NomFichier LIKE \"$file\"";
                    $rs = mysqli_query($connexion, $rq);
                    $nb = mysqli_num_rows($rs);
                    // si absent
                    if ($nb==0){
                        $rq = "INSERT INTO DTC_fichiers_open (NomFichier) VALUES (\"$file\")";
                        $rs = mysqli_query($connexion, $rq);
                        $id = mysqli_insert_id($connexion);
	                    print "<li> $file";
                        print "($id)";
                    }
                }
            }
        }
    }


?>

    </body>
</html>