<?php

// $myfile = fopen("trace_json.txt", "a") or die("Unable to open file!");

include '../php/connectdb.php';

header("Content-Type: application/json; charset=UTF-8");
$obj = json_decode($_POST["x"], false);


// Suppression de la saisie précédente
$sqlSupp='DELETE FROM `resultat` WHERE id_comp='. $obj->idComp . ' AND id_joueur='. $obj->joueur . ';';
$reqSupp = $bdd->query($sqlSupp);
$reqSupp->closeCursor();


// Pour chaque trou de la compétition, associer le score du joueur
$sql='SELECT id_trou FROM `trou` WHERE `id_comp` = '. $obj->idComp . ' ORDER BY `numero`';
$reqSlt = $bdd->query($sql);
$insertScore = "";
$lstScore = explode(",", $obj->score);
$l_nbTrou = 0;
while ($listeTrou = $reqSlt->fetch())
{ 
	$insertScore = $insertScore . '("' . $listeTrou['id_trou'] . '", "' . $obj->idComp . '", "'. $obj->joueur .'", "' . $lstScore[$l_nbTrou] .'"),';
	$l_nbTrou++;
}
$insertScore = substr($insertScore,0,strlen($insertScore) - 1) . ";";


// Création du score
$sql = 'INSERT INTO `resultat` VALUES ' . $insertScore;
// fwrite($myfile, $sql);
$reqAdd = $bdd->query($sql);
$reqAdd->closeCursor();

// fclose($myfile);
?>