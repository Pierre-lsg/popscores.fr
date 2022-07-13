<?php

// $myfile = fopen("trace_json.txt", "w") or die("Unable to open file!");

include '../php/connectdb.php';

header("Content-Type: application/json; charset=UTF-8");
$obj = json_decode($_POST["x"], false);


// Mise à jour des paramètres de l'étape
$sql = 'UPDATE `competition` SET `nbJouParEqp` = ' . $obj->nbJouParEqp . ', `nbEqpParFly` = ' . $obj->nbEqpParFly . ', `dateC` = date(\'' . $obj->dateC . '\'), `dateResultat` = date(\'' . $obj->dateResultat . '\') WHERE `competition`.`id_comp` = ' . $obj->id_comp ;

// fwrite($myfile, $sql);
// fclose($myfile);

$reqAdd = $bdd->query($sql);
$reqAdd->closeCursor();

?>