<?php
include_once("connexion.php");

$updateQueries = [
    "UPDATE Deliberation SET NatureDeliberation = 'AUTORISATION' WHERE NatureDeliberation = 'AUTORISATION RECHERCHE'",
    "UPDATE Deliberation SET NatureDeliberation = 'Autre autorisation' WHERE NatureDeliberation = 'AUTORISATION'",
    "UPDATE Deliberation SET NatureDeliberation = 'Référentiel/Règlement type/Norme' WHERE NatureDeliberation = 'Norme simplifiée'",
    "UPDATE Deliberation SET NatureDeliberation = 'Certification/Label' WHERE NatureDeliberation = 'Label'",
    "UPDATE Deliberation SET NatureDeliberation = 'Autre' WHERE NatureDeliberation = 'NATURE_DELIB start absent'"
];

// foreach ($updateQueries as $updateQuery) {
//     if (mysqli_query($connexion, $updateQuery)) {
//         echo "Correction effectuée！<br>";
//     } else {
//         echo "Erreur：" . mysqli_error($connexion) . "<br>";
//     }
// }

mysqli_close($connexion);
?>