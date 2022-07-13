<?php

// $myfile = fopen("trace_json.txt", "w") or die("Unable to open file!");
// fwrite($myfile, $sql);
// fclose($myfile);

// Paramètres d'entrée JSON
// idComp : identifiant de la compétition
// idArbitre : identifiant de l'arbitre

include '../php/connectdb.php';

header("Content-Type: application/json; charset=UTF-8");
$obj = json_decode($_POST["x"], false);

// Rattachement de la liste de joueurs
$sql = 'INSERT INTO `arbitre_competition` VALUES ' . '("' . $obj->id_comp . '", "'. $idJouEqp[0] .'", "' . $idJouEqp[1] .'"),';
$reqAdd = $bdd->query($sql);
$reqAdd->closeCursor();
?>