<?php 
	$parcours_ok = true; 
	$joueurs_ok = true; 
	$demarrer_ok = true; 
	$saisieSc_ok = false; 
	$publieSc_ok = false; 
	
	$nbTrou = 9;
	
	// Calcul du nombre de trou de la compétition
	$reponse = $bdd->query('SELECT `nbTrou` FROM `competition` WHERE id_comp = ' . $_GET["id_comp"]);
	if ($reponse->rowCount() <> 0) 
	{ 
		$donnees  = $reponse->fetch();
		$nbTrou = $donnees['nbTrou'];
	}
	$reponse->closeCursor();
	
	// Parcours et Joueurs sont inaccessible dès que la compétition a démarré (existence de Fly)
	// Le score n'est saisissable que lorsque la compétition a démarrée
	$reponse = $bdd->query('SELECT id_fly FROM flight WHERE id_comp = ' . $_GET["id_comp"]);
	if ($reponse->rowCount() <> 0) { $parcours_ok = false; $joueurs_ok = false; $saisieSc_ok = true;}
	$reponse->closeCursor();
	
	// Le lancement d'une compétition n'est possible que s'il existe des joueurs engagés
	// et si aucun score n'a été saisi
	$reponse = $bdd->query('SELECT id_comp FROM joueur_comp WHERE id_comp = ' . $_GET["id_comp"]);
	if ($reponse->rowCount() == 0) { $demarrer_ok = false; }
	$nbJoueurComp = $reponse->rowCount();
	$reponse->closeCursor();
						
	$reponse = $bdd->query('SELECT id_comp FROM resultat WHERE id_comp = ' . $_GET["id_comp"]);
	if ($reponse->rowCount() <> 0) { $demarrer_ok = false; }
	$reponse->closeCursor();
						
	// Le score peut être publié si les résultats de tous les fly a été saisi
	$reponse = $bdd->query('SELECT id_joueur FROM `resultat` WHERE id_comp = ' . $_GET["id_comp"]);
	$nbResultatSaisi = $reponse->rowCount();
	$reponse->closeCursor();
	if ($nbResultatSaisi == $nbJoueurComp * $nbTrou && $nbResultatSaisi <> 0) { $publieSc_ok = true; }
	
	// TODO : si le résultat est publié alors interdire toute modification des résultats
	// Définir les tables de suivi et de scores publiés
	$reponse = $bdd->query('SELECT id_comp FROM `classement_comp` WHERE id_comp = ' . $_GET["id_comp"]);
	if ($reponse->rowCount() <> 0) { $saisieSc_ok = false; }
	$reponse->closeCursor();
	
?>