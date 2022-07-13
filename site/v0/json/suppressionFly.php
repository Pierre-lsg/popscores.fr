<?php

// $myfile = fopen("trace_json.txt", "a") or die("Unable to open file!");
// fwrite($myfile, $sqlAdd);
// fclose($myfile);


include '../php/connectdb.php';

header("Content-Type: application/json; charset=UTF-8");
$obj = json_decode($_POST["x"], false);

// Suppression de l'ancien calcul
$sqlDel = 'DELETE FROM `flight` WHERE `flight`.`id_comp` = ' . $obj->idComp;
$reqDel = $bdd->query($sqlDel);
$reqDel->closeCursor();

?>