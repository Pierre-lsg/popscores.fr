<?php

// $myfile = fopen("trace_json.txt", "w") or die("Unable to open file!");
// fwrite($myfile, $sql);
// fclose($myfile);

include '../php/connectdb.php';

header("Content-Type: application/json; charset=UTF-8");
$obj = json_decode($_POST["x"], false);

// Récupère le club de l'équipe
$sql = 'SELECT `id_club` FROM `equipe` WHERE `id_equipe` = "' . $obj->equipe . '";';
$reqSlt = $bdd->query($sql);
if ($reqSlt->rowCount() <> 0)
{	$donnees = $reqSlt->fetch();
	$l_idClub = $donnees['id_club']; 
}
else { $l_idClub = 0; }
$reqSlt->closeCursor();

// Rattachement à la liste de joueurs
$sql = 'INSERT INTO `joueur` VALUES (NULL, "' . $obj->nom . '", "' . $obj->prenom . '", NULL, NULL, 0, ' .  $l_idClub . ', "' . $obj->equipe . '", "' . $obj->idchamp . '");';
$reqAdd = $bdd->query($sql);
$reqAdd->closeCursor();

?>