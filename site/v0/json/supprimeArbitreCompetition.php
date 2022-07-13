<?php

// $myfile = fopen("trace_json.txt", "a") or die("Unable to open file!");
// fwrite($myfile, $sqlAdd);
// fclose($myfile);

// Paramètres d'entrée JSON
// idComp : identifiant de la compétition
// idArbitre : identifiant de l'arbitre

include '../php/connectdb.php';

header("Content-Type: application/json; charset=UTF-8");
$obj = json_decode($_POST["x"], false);

// Suppression de l'ancien calcul
$sqlDel = 'DELETE FROM `arbitre_competition` a WHERE a.`id_comp` = ' . $obj->idComp . ' AND a.`id_arbitre` = '. $obj->idArbitre ;
$reqDel = $bdd->query($sqlDel);
$reqDel->closeCursor();

?>