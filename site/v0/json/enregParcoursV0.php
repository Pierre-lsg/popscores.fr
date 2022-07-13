<?php

// $myfile = fopen("trace_json.txt", "w") or die("Unable to open file!");

include '../php/connectdb.php';

header("Content-Type: application/json; charset=UTF-8");
$obj = json_decode($_POST["x"], false);


// $txt = 'DELETE FROM `trou` WHERE id_comp='. $obj->id_comp . ' ;';	


// Suppression du précédent parcours
$reponse = $bdd->query('DELETE FROM `trou` WHERE id_comp='. $obj->id_comp . ' ;');
$reponse->closeCursor();

fwrite($myfile, '|' . sizeof($obj->frjTrou));
$insert = '';

// Création du parcours
for ($i = 0 ; $i < sizeof($obj->frjTrou) ; $i++)
{
    $idTrou = $i + 1;
    $insert = $insert . ' (NULL, "", "' . $idTrou . '", "' . $obj->frjTrou[$i] . '", "", "", NULL, "", NULL, NULL, "' . $obj->parTrou[$i] . '", "0", '. $obj->id_comp . '),'; 
}
$insert = substr($insert,0,-1) . ";";

$sql = 'INSERT INTO `trou` VALUES' . $insert;

//fwrite($myfile, '|' . $sql);
//fclose($myfile);

$reponse = $bdd->query($sql);
$reponse->closeCursor();
?>
