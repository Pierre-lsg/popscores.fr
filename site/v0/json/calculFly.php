<?php

// $myfile = fopen("trace_json.txt", "a") or die("Unable to open file!");
// fwrite($myfile, $sql);
// fclose($myfile);


include '../php/connectdb.php';

header("Content-Type: application/json; charset=UTF-8");
$obj = json_decode($_POST["x"], false);


// Récupérer les 'nombres d'équipe par fly' et 'nombre de joueur par équipe' de la compétition
// Rq : une équipe peut avoir moins de joueur que le nombre maximum
$sql = 'SELECT c.nbJouParEqp, c.nbEqpParFly, cc.interne FROM competition c, championnat cc WHERE c.id_comp = ' . $obj->idComp . ' AND c.id_champ = cc.id_champ ;';
$reqSlt = $bdd->query($sql);
if ($reqSlt->rowCount() <> 0)
{
	$donnees = $reqSlt->fetch();
	$l_estInterne  = $donnees['interne'];
	$l_nbJouParEqp = $donnees['nbJouParEqp'];
	$l_nbEqpParFly = $donnees['nbEqpParFly']; 
}
else
{
	$l_nbJouParEqp = 3;
	$l_nbEqpParFly = 3;
}
$reqSlt->closeCursor();

// Récupérer l'id du Club 'Sans Club'
$sqlParam = 'SELECT `val` FROM `param` WHERE `param` = "id_sansClub"';
$reqPrm = $bdd->query($sqlParam);
if ($reqPrm->rowCount() <> 0)
{
	$donnees = $reqPrm->fetch();
	$id_sansClub = $donnees['val'];
}
else { $id_sansClub = 31; }
$reqPrm->closeCursor();

// Variables de calcul
$calculFlyOK = false;
$l_nbTentative = 0;
$l_repartirEqp = false;

// Boucle de calcul des flights
while(!$calculFlyOK)
{
	// Suppression de l'ancien calcul
	$sqlDel = 'DELETE FROM `flight` WHERE `flight`.`id_comp` = ' . $obj->idComp;
	$reqDel = $bdd->query($sqlDel);
	$reqDel->closeCursor();

	// Attribuer un indice différent pour chaque équipe
	$sql = 'SELECT `id_equipe` FROM `equipe`';
	$reqSlt = $bdd->query($sql);

	if ($reqSlt->rowCount() <> 0)
	{
		while ($donnees = $reqSlt->fetch())
		{ 
			$indiceFly = rand(1,10000);
			$sqlUpd = 'UPDATE `equipe` SET `rdmCalcFly` = "'. $indiceFly . '" WHERE `equipe`.`id_equipe` = ' . $donnees['id_equipe'] . ';';
			$reqUpd = $bdd->query($sqlUpd);
			$reqUpd->closeCursor();
		}
	}
	$reqSlt->closeCursor();

	// Trier les équipes de la compétition suivant l'indice
	$sql = '
	SELECT e.`id_equipe`, e.`nom` FROM `equipe` e WHERE e.`id_equipe` IN (SELECT DISTINCT c.`id_equipe` FROM `joueur_comp` c WHERE c.`id_comp` = '. $obj->idComp .') ORDER BY e.`rdmCalcFly`';
	$reqSlt = $bdd->query($sql);
	if ($reqSlt->rowCount() <> 0)
	{
		// Nombre d'équipe du fly
		$l_nbEquipeComp = $reqSlt->rowCount();
		
		// Nombre de fly
		if ($l_nbEquipeComp % $l_nbEqpParFly == 0) { $l_nbFlyComp = $l_nbEquipeComp / $l_nbEqpParFly; }
		else { $l_nbFlyComp = floor($l_nbEquipeComp / $l_nbEqpParFly) + 1; }
		
		// Si le dernier fly contient une équipe alors l'avant dernier fly devra céder une équipe au dernier fly. Uniquement si plus de l_nbEqpParFly dans la compétition
		if ($l_nbEquipeComp > $l_nbEqpParFly and $l_nbEquipeComp % $l_nbEqpParFly == 1)
		{ $l_repartirEqp = true; }
		
		// Créer un flypar nbr equipe
		$l_numFly 	   = 0;
		$l_nbEquipeTrt = 0;
		$l_nbEqpFly    = 0;
		$l_dernierFlyTraite = false;
		
		// Nombre de fly pour la compétition

		while ($equipeFly = $reqSlt->fetch())
		{
			// Rdg 1 : si fly complet, passage au fly suivant
			if (($l_nbEquipeTrt % $l_nbEqpParFly) == 0 and !$l_dernierFlyTraite)
			{ $l_numFly++; $l_nbEqpFly = 0; }
		
			// Rdg 2 : si avant dernier fly complet pour cause de dernier fly avec une équipe, passage au dernier fly
			if ($l_repartirEqp == true and $l_numFly == ($l_nbFlyComp - 1) and ($l_nbEqpFly == ($l_nbEqpParFly - 1))) 
			{ $l_numFly++; $l_nbEqpFly = 0; $l_dernierFlyTraite = true; }
			
			$l_nbEquipeTrt++;
			$l_nbEqpFly++;
			
			// Pour chaque joueur de l'équipe engagé dans la compétition
			$sqlJou = 'SELECT `id_joueur` FROM `joueur_comp` WHERE `id_comp` = ' . $obj->idComp . ' AND `id_equipe` = ' . $equipeFly['id_equipe'] . ' ORDER BY `id_joueur` LIMIT ' . $l_nbJouParEqp . ';';

			$reqJou = $bdd->query($sqlJou);
			while ($joueurFly = $reqJou->fetch())
			{
				$sqlAdd = 'INSERT INTO `flight` VALUES (NULL, '. $l_numFly .', ' . $obj->idComp . ', ' . $equipeFly['id_equipe'] . ', ' . $joueurFly['id_joueur'] . '); ';
				$reqAdd = $bdd->query($sqlAdd);
				$reqAdd->closeCursor();
			}
		}
	}
	$reqSlt->closeCursor();
	
	// Contrôle du flight calculé
	$l_numero = 0;
	$sql = 'SELECT f.numero, e.id_club FROM flight f, equipe e WHERE f.id_comp = ' . $obj->idComp . ' AND f.id_equipe = e.id_equipe AND e.id_club <> ' . $id_sansClub . ' GROUP BY e.id_equipe ORDER BY f.numero, e.id_club ';
	$reqSlt = $bdd->query($sql);

	if ($reqSlt->rowCount() <> 0)
	{
		$calculFlyOK = true;	
		while ($donnees = $reqSlt->fetch())
		{ 
			if ($l_numero !== $donnees['numero'])
			{	$l_numero = $donnees['numero'];
				$l_idclub = 0;}
			
			if ($l_idclub == $donnees['id_club'])
			{ $calculFlyOK = false; break; }
			else { $l_idclub = $donnees['id_club'];}
		}
	}
	$reqSlt->closeCursor();
	
	// Si compétition interne, pas de contrôle des flys
	if ($l_estInterne == 1)
	{ $calculFlyOK = true;}
	
	// Si plus de 100 tentatives de calcul de Fly alors on sort de la boucle
	$l_nbTentative++;
	if (l_nbTentative > 100) {$calculFlyOK = true;}		
}

?>