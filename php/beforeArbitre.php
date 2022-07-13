<?php
	session_start();
	
	/* Si la connexion n'existe pas, renvoyer vers la page d'accueil */
	if (isset($_SESSION['connexion']))
	{
		if ($_SESSION['connexion'] <> 'oui')
		{
			/* A modifier sur le site popscores */
			header('Location: /accueilArbitre.php');
			exit;
		}
	}
	else 
	{
		/* A modifier sur le site popscores */
		header('Location: /accueilArbitre.php');
		exit;
	}
?>