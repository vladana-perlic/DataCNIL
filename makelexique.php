<?php
    include("connexion.php");
    include("fonctions.php");
    $increment = 5;
    $timestart = time();
    if (isset($_GET['first'])) {$first=$_GET['first'];$last=$first+$increment;}  else {$first=1;$last=$first+$increment;}
    print "Indexation lexicale des délibérations $first à $last<br/>";
    // recup des textes
    $rqdelib = "SELECT IDDelib, TitreLong, Contenu FROM Deliberation WHERE IDDelib>=$first AND IDDelib<$last";
    $rsdelib = mysqli_query($connexion,$rqdelib);
    $nbrows = mysqli_num_rows($rsdelib);
    while ($lgdelib = mysqli_fetch_row($rsdelib)){
        $tablex=array();
        $iddelib = $lgdelib[0];
        //print "<textarea cols=25 rows=5>";
        //print "Analyse de la délib #$iddelib\n";
        // on enleve les tags html
        $texteentier = strip_tags($lgdelib[1]." ".$lgdelib[2]);
        // on anonymise les mails
        $texteentier = preg_replace("/^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/","anonyme@mail",$texteentier, -1, $nb);
        if ($nb>0) print " $nb mail".(($nb>0)?"s":"")."anonymisé".(($nb>0)?"s":"")."\n";
        // on enleve les entities moches
        $texteentier = str_replace(array("&amp;","&lt;","&gt;", "+/-")," ",$texteentier);
        // on enleve les caractères inutiles
        $texteentier = str_replace(array(",","?",";",".",":","'","!", "CONTENU vide", 
                                        "’", "(", ")", "»", "«", "\n", "\t", "\"", "…", 
                                        "+", "*", "+","[", "]", "#", "=","Þ","•","•-",
                                        "-","€","—","―","‘","	","²","±","°","®","¬",
                                        "·","©","¨","§","…","≥","□","►","","","","�","/","&","_"),
                                " ",
                                $texteentier);
        $tabtok =explode(" ", $texteentier);
        foreach($tabtok as $tok){
            $tok = strtolower(trim($tok, " \n\r\t\v\x00-\"“”"));
            if ((!in_array($tok, $motsvides))&&($tok!="")&&(strlen($tok)>2)&&(!preg_match("/[0-9]/",$tok))&&(strlen($tok)<80)){
                    if (isset($tablex[$tok])) $tablex[$tok]++;
                    else $tablex[$tok]=1;
                }
        }
        $cptadd = 0;
        $cptnew = 0;
        foreach($tablex as $key => $value){
            $cptadd++;
            //trouve le tok dans le lexique
            $rqgraphie = "SELECT IDToken, NbOcc, NbTextOcc FROM Token where Graphie LIKE \"$key\"";
            $rsgraphie = mysqli_query($connexion, $rqgraphie);
            if ($rsgraphie!=FALSE){
                if (mysqli_num_rows($rsgraphie)>0){
                    $lggraphie = mysqli_fetch_row($rsgraphie);
                    $idtoken = $lggraphie[0];
                    // mise à jour nb occ total si token existe
                    $nbocctoken = $lggraphie[1] + $value;
                    $nbtextocctoken = $lggraphie[2] +1;
                    
					$rqtokupdate = "UPDATE Token SET NbOcc = $nbocctoken, NbTextOcc = $nbtextocctoken WHERE IDToken = $idtoken";
                	$rstokupdate = mysqli_query($connexion, $rqtokupdate);
                    if ($idtoken==0) { print "\nToken[$key] a un identifiant récupéré nul $idtoken dans $iddelib";exit(0);}
                }
                else {
                    $lginsertg = "INSERT INTO Token (Graphie, NbOcc, NbTextOcc) VALUES (\"$key\", $value, 1)";
                    $rsinsertg = mysqli_query($connexion, $lginsertg);
                    $idtoken = mysqli_insert_id($connexion);
                    //$idtoken = $lggraphie[0];
                    if ($idtoken==0) { print "\nToken[$key] a un identifiant inséré nul $idtoken dans $iddelib"."<br/>$lginsertg";exit(0);}
                    //print "\nToken[$key] créé $idtoken";
                    $cptnew++;
                }
                $rqtokdelib = "INSERT INTO Token2Deliberation (IDToken, IDDelib, NbOcc) VALUES ($idtoken, $iddelib, $value)";
                $rstokdelib = mysqli_query($connexion,$rqtokdelib);
            }
        }
        //print_r($tablex);
        //print "$cptadd token".(($cptadd>1)?"s":"")." ajouté".(($cptadd>1)?"s":"")."\n$cptnew nouveau".(($cptnew>1)?"x":"")." mot".(($cptnew>1)?"s":"")."\n";
        //print "</textarea>";
    }
$timeend = time();
if ($nbrows<$increment) $refresh = "<H1>Analyse complète</H1>";
	else $refresh = "<meta http-equiv=\"refresh\" content=\"0;URL=makelexique.php?first=$last\">";

print "<br/>Temps moyen de calcul : ".number_format(($timeend-$timestart)/$increment,2,","," ")."s/delib";
print "<br/>Temps total de calcul : ".number_format($timeend-$timestart,2,","," ")."s";
print "<br/>Temps restant estimé : ".gmdate("H:i:s",((($timeend-$timestart)/$increment)*(24933-$last)));
print "<br/>Estimated time of arrival : ".gmdate("H:i:s", $timeend+((($timeend-$timestart)/$increment)*(24933-$last)));
print "<br/>Pourcentage fait : ".number_format(($last*100/24933),2,","," ")."%";

print $refresh;