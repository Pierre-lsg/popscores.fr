<?php

// $myfile = fopen("trace_json.txt", "w") or die("Unable to open file!");
// fwrite($myfile, $sql);
// fclose($myfile);

include '../php/connectdb.php';

header("Content-Type: application/json; charset=UTF-8");
$obj = json_decode($_POST["x"], false);

// Suppression de la précédente liste
$sql='DELETE FROM `joueur_comp` WHERE id_comp='. $obj->id_comp . ' ;';
$reqSupp = $bdd->query($sql);
$reqSupp->closeCursor();

// Rattachement de la liste de joueurs
$sql = 'INSERT INTO `joueur_comp` VALUES ';
$lstJoueurEquipe = explode(",", $obj->listeJoueur);
foreach ($lstJoueurEquipe as $idJouEqp) 
{
	$idJouEqp = explode("#", $idJouEqp);
	$sql = $sql . '("' . $obj->id_comp . '", "'. $idJouEqp[0] .'", "' . $idJouEqp[1] .'"),';
}
$sql = substr($sql,0,strlen($sql) - 1) . ";";
$reqAdd = $bdd->query($sql);
$reqAdd->closeCursor();
?>