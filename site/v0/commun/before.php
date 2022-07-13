<?php 
	include("../../php/connectdb.php");  
	$sql = "SELECT id_comp FROM organisation_comp WHERE id_comp = " . $_GET['id_comp'] . " AND id_org = " . $_SESSION['id_org'] . ";";
	$reponse = $bdd->query($sql);
	if ($reponse->rowCount() == 0)
	{
		header('Location: index.php');
		exit();
	}
?>
