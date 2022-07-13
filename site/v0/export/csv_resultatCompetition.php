<?php
		include '../php/connectdb.php';
		
		if (isset($_GET['id_comp'])) {$l_idComp = $_GET['id_comp']; }
		else {$l_idComp = '10';}
		
		$nbTrou = 9;
		
		// Calcul du nombre de trou de la compétition
		$reponse = $bdd->query('SELECT `nbTrou` FROM `competition` WHERE id_comp = ' . $l_idComp);
		if ($reponse->rowCount() <> 0) 
		{ 
			$donnees  = $reponse->fetch();
			$nbTrou = $donnees['nbTrou'];
		}
		$reponse->closeCursor();
		
		
		header('Content-Type: text/csv; charset=utf-8');  
		header('Content-Disposition: attachment; filename=resultatCompetition.csv');  
		$output = fopen("php://output", "w");  
		
		// Entête du tableau
		$enteteTabResComp = array('Fly', 'Equipe', 'Nom', 'Prenom', '#joueur');
		
		for ($idTrou = 1 ; $idTrou <= $nbTrou ; $idTrou++)
		{ array_push($enteteTabResComp, 'Trou '.$idTrou); }
		
		array_push($enteteTabResComp, 'Nb coups', 'Score');
		
		//fputcsv($output, array('Fly', 'Equipe', 'Nom', 'Prenom', '#joueur', 'Trou 1', 'Trou 2', 'Trou 3', 'Trou 4', 'Trou 5', 'Trou 6', 'Trou 7', 'Trou 8', 'Trou 9', 'Nb coups', 'Score'));  
		
		fputcsv($output, $enteteTabResComp);  

		// Calcul du par total
		$l_parTotal = 0;
		$sqlPar = 'SELECT SUM(par) AS total FROM trou WHERE id_comp = ' . $l_idComp . ' ;';
		$sumPar = $bdd->query($sqlPar);
		if ($sumPar->rowCount() <> 0)
		{ 
			$par = $sumPar->fetch(); 
			$l_parTotal = $par['total']; 
		}
		$sumPar->closeCursor();

		// Liste des joueurs du fly
		$query = 'SELECT f.numero, e.nom AS eqp_nom, j.prenom, j.nom, j.id_joueur AS idJoueur FROM `flight` f, `equipe` e, `joueur` j WHERE f.id_comp = ' . $l_idComp . ' AND f.id_equipe = e.id_equipe AND f.id_joueur = j.id_joueur ORDER BY f.numero, f.id_equipe';  
			
		$req = $bdd->query($query);
		while ($row = $req->fetch(PDO::FETCH_ASSOC)) 
		{  
			$l_total = 0;
			$sqlTrou = 'SELECT t.numero, r.score FROM `resultat` r, trou t WHERE r.id_comp = ' . $l_idComp . ' AND r.id_joueur = ' . $row['idJoueur'] . ' AND r.id_trou = t.id_trou ORDER BY t.numero ;';
			$reqTrou = $bdd->query($sqlTrou);
			while ($trou = $reqTrou->fetch())
			{
				$nomElt = "Trou " . $trou['numero'];
				$row[$nomElt] = $trou['score'];
				$l_total += $trou['score'];
			}
			$row["Nb coups"] = $l_total;
			$row["Score"] = $l_total - $l_parTotal;
			
			fputcsv($output, $row);  
		}  
		fclose($output); 
?>