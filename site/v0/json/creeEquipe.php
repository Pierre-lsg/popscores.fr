<?php

// $myfile = fopen("trace_json.txt", "w") or die("Unable to open file!");
// fwrite($myfile, $sql);
// fclose($myfile);

include '../php/connectdb.php';

header("Content-Type: application/json; charset=UTF-8");
$obj = json_decode($_POST["x"], false);

// Rattachement à la liste de joueurs
$sql = 'INSERT INTO `equipe` VALUES (NULL, "' . $obj->nom . '", NULL, 0, "' . $obj->club . '", 1);';
$reqAdd = $bdd->query($sql);
$reqAdd->closeCursor();

?>