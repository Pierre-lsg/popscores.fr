<?php
		include '../php/connectdb.php';
		
		if (isset($_GET['id_comp'])) {$l_idComp = $_GET['id_comp']; }
		else {$l_idComp = '10';}
		
		$l_classement = 1;
		$l_cptLigne = 1;
		$l_score = 0;

		header('Content-Type: text/csv; charset=utf-8');  
		header('Content-Disposition: attachment; filename=classementEquipe.csv');  
		$output = fopen("php://output", "w");  
		fputcsv($output, array('Classement', 'Equipe', 'Points', 'Resultat'));  
		$query = 'SELECT "" AS classement, e.nom, c.score, c.resultat FROM classement_comp c, equipe e WHERE c.id_comp = ' . $l_idComp . ' AND c.id_catClass = 2 AND e.id_equipe = c.id_cat ORDER BY(c.score) DESC, nom';  

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