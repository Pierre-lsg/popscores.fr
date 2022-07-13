<?php
		include '../php/connectdb.php';
		
		if (isset($_GET['id_champ'])) {$l_idChamp = $_GET['id_champ']; }
		else {$l_idChamp = '5';}

		$l_classement = 1;
		$l_cptLigne = 1;
		$l_score = 0;

		header('Content-Type: text/csv; charset=utf-8');  
		header('Content-Disposition: attachment; filename=classementGeneralJoueur.csv');  
		$output = fopen("php://output", "w");  
		fputcsv($output, array('Classement', 'Joueur', 'Points'));  
		$query = 'SELECT "" AS classement, CONCAT(j.prenom, " " ,j.nom) AS joueur, c.score FROM classement_champ c, joueur j WHERE c.id_champ = ' . $l_idChamp . ' AND c.id_catClass = 1 AND j.id_joueur = c.id_cat ORDER BY(c.score) DESC, joueur';  

		$req = $bdd->query($query);
		while ($row = $req->fetch(PDO::FETCH_ASSOC)) 
		{  
			if ($l_score <> $row['score']) 
			{$l_classement = $l_cptLigne; $l_score = $row['score'];}
			
			$row['classement'] = $l_classement;
			fputcsv($output, $row);  
			
			$l_cptLigne += 1;
			
		}  
		fclose($output); 
?>