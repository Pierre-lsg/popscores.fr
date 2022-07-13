<?php 
	include("../../php/connectdb.php");  
	$sql = "SELECT id_comp FROM arbitre_competition WHERE id_comp = " . $_SESSION['id_comp'] . " AND id_arbitre = " . $_SESSION['id_arbitre'] . ";";
	$reponse = $bdd->query($sql);
	
	if ($reponse->rowCount() == 0)
	{
		session_destroy(); 

		header('Location: /accueilArbitre.php');
		exit();
	}
?>
