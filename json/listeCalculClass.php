<?php

// $myfile = fopen("trace_json.txt", "w") or die("Unable to open file!");

include '../php/connectdb.php';

header("Content-Type: application/json; charset=UTF-8");
$obj = json_decode($_POST["x"], false);
$lstClass = '';

// Récupération du classement 
$reponse = $bdd->query('SELECT classement, points FROM ref_classement WHERE id_champ ="' . $obj->id_champ . '" AND id_catClass="'. $obj->id_catClass . '" ;');
if ($reponse->rowCount() <> 0)
{
	while ($donnees = $reponse->fetch())
	{ $lstClass .= '{"classement":"' . $donnees['classement'] . '", "points":"' . $donnees['points'] . '"},' ; }
	
	$lstClass = substr($lstClass,0,strlen($lstClass)-1);
	$lstClass = "[" . $lstClass ."]";
}
else { $lstClass = ''; }

$reponse->closeCursor();

// $txt = $lstClass;	

// fwrite($myfile, $txt);
// fclose($myfile);



 echo $lstClass;
?>