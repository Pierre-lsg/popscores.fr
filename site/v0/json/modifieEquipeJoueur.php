<?php

// $myfile = fopen("trace_json.txt", "w") or die("Unable to open file!");
// fwrite($myfile, $sql);
// fclose($myfile);

include '../php/connectdb.php';

header("Content-Type: application/json; charset=UTF-8");
$obj = json_decode($_POST["x"], false);

// Rattachement à la liste de joueurs
$sql = 'UPDATE `joueur` SET `id_equipe` = "' . $obj->idEquipe . '" WHERE `joueur`.`id_joueur` = "' . $obj->idJoueur . '";';
$reqMod = $bdd->query($sql);
$reqMod->closeCursor();

?>