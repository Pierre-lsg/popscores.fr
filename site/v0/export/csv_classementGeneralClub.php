<?php
		include '../php/connectdb.php';
		
		if (isset($_GET['id_champ'])) {$l_idChamp = $_GET['id_champ']; }
		else {$l_idChamp = '5';}

		$l_classement = 1;
		$l_cptLigne = 1;
		$l_score = 0;

		header('Content-Type: text/csv; charset=utf-8');  
		header('Content-Disposition: attachment; filename=classementGeneralClub.csv');  
		$output = fopen("php://output", "w");  
		fputcsv($output, array('Classement', 'Club', 'Points'));  
		$query = 'SELECT "" AS classement, cl.nom, c.score FROM classement_champ c, club cl WHERE c.id_champ = ' . $l_idChamp . ' AND c.id_catClass = 3 AND cl.id_club = c.id_cat ORDER BY(c.score) DESC, nom';  

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