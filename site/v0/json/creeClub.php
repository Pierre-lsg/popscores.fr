<?php

// $myfile = fopen("trace_json.txt", "w") or die("Unable to open file!");
// fwrite($myfile, $sql);
// fclose($myfile);

include '../php/connectdb.php';

header("Content-Type: application/json; charset=UTF-8");
$obj = json_decode($_POST["x"], false);

// Rattachement à la liste de joueurs
$sql = 'INSERT INTO `club` VALUES (NULL, "' . $obj->nom . '", "' . $obj->logo . '", "' . $obj->descriptif . '");';
$reqAdd = $bdd->query($sql);
$reqAdd->closeCursor();

?>