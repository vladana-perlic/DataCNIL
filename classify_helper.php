<?php
    // session_start();
    // if (isset($_SESSION['post'])) $_POST = $_SESSION['post'];
    // include_once("trace.php");
    
    // $ref = $_GET['ref'];
    // if ($ref == "all") {
    //     $contraintetemp = "1";
    // } else if (strlen($ref) > 4) {
    //     $mois = substr($ref, 0, 2);
    //     $annee = substr($ref, 3, 4);
    //     $contraintetemp = "AnneeTexte LIKE \"$annee\" AND MoisTexte LIKE \"$mois\"";
    // } else {
    //     $contraintetemp = "AnneeTexte LIKE \"$ref\"";
    // }
    // include_once("connexion.php");
    // include_once("fonctions.php");

    if (isset($_GET['tab']) && isset($_GET['ref'])){
        // $rqid = "SELECT NomFichier, DateTexte, TitreLong, Contenu FROM Deliberation WHERE " . $_SESSION['contraintes'] . " AND $contraintetemp  ORDER BY DateTexte";
        // $rsid = mysqli_query($connexion, $rqid);

        session_start();
        if (isset($_SESSION['post'])) $_POST = $_SESSION['post'];
        include_once("trace.php");
        $ref = $_GET['ref'];
        if ($ref == "all") {
            $contraintetemp = "1";
        } else if (strlen($ref) > 4) {
            $mois = substr($ref, 0, 2);
            $annee = substr($ref, 3, 4);
            $contraintetemp = "AnneeTexte LIKE \"$annee\" AND MoisTexte LIKE \"$mois\"";
        } else {
            $contraintetemp = "AnneeTexte LIKE \"$ref\"";
        }
        include_once("connexion.php");
        include_once("fonctions.php");

        $tab=$_GET['tab'];
        if ($tab == 'profile'){
            $favorableData = []; 
            $rqid = "SELECT NomFichier, DateTexte, TitreLong, Contenu FROM Deliberation WHERE " . $_SESSION['contraintes'] . " AND $contraintetemp  ORDER BY DateTexte";
            $rsid = mysqli_query($connexion, $rqid); 
    
            while ($lgid = mysqli_fetch_row($rsid)) {
                if (stripos($lgid[2], "avis favorable") !== false || stripos($lgid[3], "avis favorable") !== false) {
                    $favorableData[] = $lgid[0]; 
                }
            }
            if (empty($favorableData)) {
                echo "None"; 
            } else {
                foreach ($favorableData as $data) {
                    echo cnil2html($data)."\n\n<br/><br/><br/><br/>\n\n"; 
                }
            }
        } else if ($tab =='contact'){
            $defavorableData = []; 
            $rqid = "SELECT NomFichier, DateTexte, TitreLong, Contenu FROM Deliberation WHERE " . $_SESSION['contraintes'] . " AND $contraintetemp  ORDER BY DateTexte";
            $rsid = mysqli_query($connexion, $rqid); 

            while ($lgid = mysqli_fetch_row($rsid)) {
                if (stripos($lgid[2], "avis défavorable") !== false || stripos($lgid[3], "avis défavorable") !== false) {
                    $defavorableData[] = $lgid[0]; 
                }
            }

            if (empty($defavorableData)) {
                echo "None"; 
            } else {
                foreach ($defavorableData as $data) {
                    echo cnil2html($data)."\n\n<br/><br/><br/><br/>\n\n"; 
                }
            }
        } else if ($tab == 'neutre'){
            $neutreData = [];
            $rqid = "SELECT NomFichier, DateTexte, TitreLong, Contenu FROM Deliberation WHERE " . $_SESSION['contraintes'] . " AND $contraintetemp  ORDER BY DateTexte";
            $rsid = mysqli_query($connexion, $rqid); 

            while ($lgid = mysqli_fetch_row($rsid)) {
                if (stripos($lgid[2], "avis favorable") !== false || stripos($lgid[3], "avis favorable") !== false) {
                    $favorableData[] = $lgid[0];
                } elseif (stripos($lgid[2], "avis défavorable") !== false || stripos($lgid[3], "avis défavorable") !== false) {
                    $defavorableData[] = $lgid[0];
                } else {
                    $neutreData[] = $lgid[0];
                }
            }
            if (empty($neutreData)) {
                echo "None"; 
            } else {
                foreach ($neutreData as $data) {
                    echo cnil2html($data)."\n\n<br/><br/><br/><br/>\n\n"; 
                }
            }
        }

    }
    

    ?>