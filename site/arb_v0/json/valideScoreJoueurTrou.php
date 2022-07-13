<?php

// $myfile = fopen("trace_json.txt", "a") or die("Unable to open file!");
// fwrite($myfile, $sql);
// fclose($myfile);

include '../php/connectdb.php';

header("Content-Type: application/json; charset=UTF-8");
$obj = json_decode($_POST["x"], false);


// Suppression de la saisie précédente
$sqlSupp='DELETE FROM `resultat` WHERE id_comp='. $obj->idComp . ' AND id_joueur='. $obj->joueur . ' AND id_trou='. $obj->trou . ';';
$reqSupp = $bdd->query($sqlSupp);
$reqSupp->closeCursor();


// Création du score
$sql = 'INSERT INTO `resultat` VALUES ("' . $obj->trou . '", "' . $obj->idComp . '", "'. $obj->joueur .'", "' . $obj->score .'")';
$reqAdd = $bdd->query($sql);
$reqAdd->closeCursor();

?>

