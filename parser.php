<html>
    <head>
        <title>DATA CNIL / parser</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="style/style.css">
    </head>
    <body>
    <H1>DATA CNIL / parser</H1>

<?php
    include("fonctions.php");
    include("connexion.php");
    $dir = "./dataxml/cnil/";
    $compteur = 0;
    // recupere les fichiers indexés non parsés
    $rqfiles = "SELECT ID_fichier, NomFichier FROM DTC_fichiers_open WHERE ID_Fichier NOT IN (SELECT IDFichier FROM Deliberation WHERE 1)";
    $rsfiles = mysqli_query($connexion, $rqfiles);
    $nbfichiers = mysqli_num_rows($rsfiles);
    print "<br/>$nbfichiers fichiers à traiter";
    while(($lgfile =mysqli_fetch_row($rsfiles))&&($compteur<100)){
        $compteur++;
        $nomfichier = $lgfile[1];
        $idfichier = $lgfile[0];
        print "<H3>$compteur</H3>";
        //print_r($lgfile);
        // lecture du fichier
        $contenu = file_get_contents($dir.$nomfichier);
        //print $contenu;
        $id = recupnoeud($contenu, "ID");
        $naturedocument = recupnoeud($contenu, "NATURE");
        $titre = recupnoeud($contenu, "TITRE");
        $titrelong = recupnoeud($contenu, "TITREFULL");
        $numero = recupnoeud($contenu, "NUMERO");
        $nature_delib = recupnoeud($contenu, "NATURE_DELIB");
        $datetexte = recupnoeud($contenu, "DATE_TEXTE");
                $anneetexte = substr($datetexte,0,4);
                $moistexte = substr($datetexte, 5,2);
        $datepubli = recupnoeud($contenu, "DATE_PUBLI");
                $anneepubli =substr($datepubli,0,4);
                $moispubli = substr($datepubli,5,2);
        $contenudelib = recupnoeud($contenu, "CONTENU");

        $rqins = "INSERT INTO Deliberation 
                    (IDCNIL, 
                    IDFichier, 
                    NatureDocument, 
                    Titre, 
                    TitreLong, 
                    Numero, 
                    NatureDeliberation, 
                    DateTexte,
                    AnneeTexte, 
                    MoisTexte,
                    DatePublication, 
                    AnneePublication,
                    MoisPublication,
                    Contenu, 
                    NomFichier) 
            VALUES (\"$id\",
                    $idfichier,
                    \"$naturedocument\",
                    \"$titre\",
                    \"$titrelong\",
                    \"$numero\",
                    \"$nature_delib\", 
                    \"$datetexte\",
                    \"$anneetexte\",
                    \"$moistexte\",
                    \"$datepubli\",
                    \"$anneepubli\",
                    \"$moispubli\",
                    \"$contenudelib\",
                    \"$nomfichier\")";
        $rsins = mysqli_query($connexion,$rqins);
        $idins = mysqli_insert_id($connexion);
        //print_r($rsins);
        //print "<textarea>$rqins</textarea>";
        print "$nomfichier($idins)";
        if ($idins==0) print "<textarea>$rqins</textarea>".strlen($contenudelib);
    }

    if ($nbfichiers>10) print "<meta http-equiv=\"refresh\" content=\"0;URL=parser.php\">";

    ?>

 
    </body>
</html>
