<?php

// $myfile = fopen("trace_json.txt", "w") or die("Unable to open file!");
// fwrite($myfile, $majSql);
// fclose($myfile);

include '../php/connectdb.php';

header("Content-Type: application/json; charset=UTF-8");
$obj = json_decode($_POST["x"], false);


$joueursE1 = array(); $i1=0;
// Liste des joueurs de l'équipe 1
$sql='SELECT id_joueur FROM `flight` WHERE id_comp = "' . $obj->idComp . '" and id_equipe = "' . $obj->equipe1 . '";';
$reqSltE1 = $bdd->query($sql);
while ($lstJoueur1 = $reqSltE1->fetch())
{ 
	$joueursE1[$i1] = $lstJoueur1['id_joueur'];
	$i1++;
}
$reqSltE1->closeCursor();


$joueursE2 = array(); $i2=0;
// Liste des joueurs de l'équipe 2
$sql='SELECT id_joueur FROM `flight` WHERE id_comp = "' . $obj->idComp . '" and id_equipe = "' . $obj->equipe2 . '";';
$reqSltE2 = $bdd->query($sql);
while ($lstJoueur2 = $reqSltE2->fetch())
{ 
	$joueursE2[$i2] = $lstJoueur2['id_joueur'];
	$i2++;
}
$reqSltE2->closeCursor();


$majSql = '';
// Calcul mise à jour Fly 1
$i=0;
$sql='SELECT id_fly FROM `flight` WHERE id_comp = "' . $obj->idComp . '" and id_equipe = "' . $obj->equipe1 . '";';
$reqSltF1 = $bdd->query($sql);
while ($lstFly1 = $reqSltF1->fetch())
{ 
	$majSql = $majSql . 'UPDATE flight SET `id_equipe` = "' . $obj->equipe2 . '", `id_joueur` = "' . $joueursE2[$i] . '" WHERE `id_fly` = "' . $lstFly1['id_fly'] . '"; ';	
	$i++;
}
$reqSltF1->closeCursor();


// Calcul mise à jour Fly 2
$i=0;
$sql='SELECT id_fly FROM `flight` WHERE id_comp = "' . $obj->idComp . '" and id_equipe = "' . $obj->equipe2 . '";';
$reqSltF2 = $bdd->query($sql);
while ($lstFly2 = $reqSltF2->fetch())
{ 
	$majSql = $majSql . 'UPDATE flight SET `id_equipe` = "' . $obj->equipe1 . '", `id_joueur` = "' . $joueursE1[$i] . '" WHERE `id_fly` = "' . $lstFly2['id_fly'] . '"; ';	
	$i++;
}
$reqSltF2->closeCursor();


// Mise à jour du fly
$reqMod = $bdd->query($majSql);
$reqMod->closeCursor();

?>