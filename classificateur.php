<?php
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

// $rqid = "SELECT NomFichier, DateTexte, TitreLong, Contenu FROM Deliberation WHERE " . $_SESSION['contraintes'] . " AND $contraintetemp  ORDER BY DateTexte";
// $rsid = mysqli_query($connexion, $rqid);

?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classificateur</title>
    <link rel="stylesheet" href="./pkg/bootstrap-4.6.2-dist/css/bootstrap.min.css">
    <script src="./pkg/jquery-3.7.1/jquery-3.7.1.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
</head>
<body>
<h1 align="center">DATA CNIL: Classificateur</h1>

    <div class="container">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-toggle="tab" data-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">*</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-tab" data-toggle="tab" data-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Favorable</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="contact-tab" data-toggle="tab" data-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Défavorable</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="neutre-tab" data-toggle="tab" data-target="#neutre" type="button" role="tab" aria-controls="neutre" aria-selected="false">Neutre</button>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">

            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                Ce classificateur de délibérations est un outil conçu pour analyser et catégoriser les délibérations comme positives, négatives ou neutres. 
                <br/>Il facilite l'analyse des opinions exprimées dans les délibérations et aide à évaluer l'impact émotionnel ou réactif des délibérations sur les parties prenantes ou le public concerné.
            </div>
            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

            </div>
            <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                
            </div>
            <div class="tab-pane fade" id="neutre" role="tabpanel" aria-labelledby="neutre-tab">
          
            </div>
        </div>
    </div>
    <script>
        $("#profile-tab").on("click",function(){
            var params=new URLSearchParams(window.location.search);
            var ref=params.get("ref");
            console.log(ref)
            $.ajax({
                url:"classify_helper.php",
                method:"get",
                data:"tab=profile&ref="+ref,
                dataType:"html",
                success:function(response,status){
                    console.log(response)
                    $("#profile").html(response);
                }
            })
        })

        $("#contact-tab").on("click",function(){
            var params=new URLSearchParams(window.location.search);
            var ref=params.get("ref");
            console.log(ref)
            $.ajax({
                url:"classify_helper.php",
                method:"get",
                data:"tab=contact&ref="+ref,
                dataType:"html",
                success:function(response,status){
                    console.log(response)
                    $("#contact").html(response);
                }
            })
        })

        $("#neutre-tab").on("click",function(){
            var params=new URLSearchParams(window.location.search);
            var ref=params.get("ref");
            console.log(ref)
            $.ajax({
                url:"classify_helper.php",
                method:"get",
                data:"tab=neutre&ref="+ref,
                dataType:"html",
                success:function(response,status){
                    console.log(response)
                    $("#neutre").html(response);
                }
            })
        })
    </script>
<script src="./pkg/bootstrap-4.6.2-dist/js/bootstrap.min.js"></script>
</body>
</html>
