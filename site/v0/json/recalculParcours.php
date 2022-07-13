<?php

// $myfile = fopen("trace_json.txt", "a") or die("Unable to open file!");
// fwrite($myfile, $sql);
// fclose($myfile);

include '../php/connectdb.php';

header("Content-Type: application/json; charset=UTF-8");
$obj = json_decode($_POST["x"], false);

// Mise à jour du parcours
$sql = 'UPDATE `competition` SET `nbTrou` = ' . $obj->nbTrou . ' WHERE `competition`.`id_comp` = ' . $obj->id_comp ;

$reqAdd = $bdd->query($sql);
$reqAdd->closeCursor();

?>
