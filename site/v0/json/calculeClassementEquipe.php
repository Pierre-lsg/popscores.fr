<?php

// $myfile = fopen("/home/pierre/www/popscores_old/site/v0/json/trace_json.txt", "w") or die("Unable to open file!");
// fwrite($myfile, 'fff');
// fclose($myfile);

include '../php/connectdb.php';

header("Content-Type: application/json; charset=UTF-8");
$obj = json_decode($_POST["x"], false);
$lstClass = '';
$insClass = '';

// Suppression du classement précédent
$sqlSupp='DELETE FROM `classement_comp` WHERE id_comp='. $obj->idComp . ' AND id_catClass = 2;';
$reqSupp = $bdd->query($sqlSupp);
$reqSupp->closeCursor();


// Chargement de la table des scores
$pointsClassement = [];
$sqlCla='SELECT classement, points FROM `ref_classement` WHERE id_champ='. $obj->idChamp . ' AND id_catClass = 2;';
$reqSCla = $bdd->query($sqlCla);
while ($donnees = $reqSCla->fetch())
{ $pointsClassement[$donnees['classement']] = $donnees['points']; }
$reqSCla->closeCursor();


// Calcul du par total
$l_parTotal = 0;
$sqlPar = 'SELECT SUM(par) AS total FROM trou WHERE id_comp = ' . $obj->idComp . ' ;';
$sumPar = $bdd->query($sqlPar);
if ($sumPar->rowCount() <> 0)
{ 
	$par = $sumPar->fetch(); 
	$l_parTotal = $par['total']; 
}
$sumPar->closeCursor();

// Paramètres du championnat
$l_modeCalculPoints = "";
$sqlPar = 'SELECT `calculPoints` FROM `championnat` WHERE id_champ = ' . $obj->idChamp . ' ;';
$reqPar = $bdd->query($sqlPar);
if ($reqPar->rowCount() <> 0)
{ 
	$res = $reqPar->fetch(); 
	$l_modeCalculPoints = $res['calculPoints']; 
}
$reqPar->closeCursor();

// Nombre de joueurs par équipe pour la compétition
$l_nbJoueurParEquipe = 0;
$sqlCmp = 'SELECT `nbJouParEqp` FROM `competition` WHERE `id_comp` = ' . $obj->idComp . ' ;';
$sumCmp = $bdd->query($sqlCmp);
if ($sumCmp->rowCount() <> 0)
{ 
	$competition = $sumCmp->fetch(); 
	$l_nbJoueurParEquipe = $competition['nbJouParEqp']; 
}
$sumCmp->closeCursor();

// Déclaration des variables 
$classFinal   = array();
$equipe       = '';
$nbptsclass   = 0; 
$nbptsclassC  = 0; 
$classement   = 0;
$classementC  = 0;
$nbCoupsPrec  = -1;
$nbCoupsPrecC = -1;
$nbPtsPrec    = 0;
$nbPtsRepart  = 0;
$nbPtsPrecC   = 0;
$nbPtsRepartC = 0;
$nbEquipeEgal = 0;
$nbEquipeEgalC 	= 0;
$estEgalite   = false;
$estEquipePrecC = false;

// ---------------------------------------------//
// -- Calcul du résultat pour la compétition -- //
// ---------------------------------------------//

$sql='SELECT e.nom, e.estCalculChampionnat AS eqpChamp, j.id_equipe, SUM(r.score) AS points, cl.logo FROM equipe e, joueur_comp j, resultat r, club cl WHERE j.id_comp = '. $obj->idComp . ' AND r.id_comp = '. $obj->idComp . ' AND j.id_joueur = r.id_joueur AND e.id_equipe = j.id_equipe AND cl.id_club = e.id_club GROUP BY j.id_equipe ORDER BY SUM(r.score)';	
$req = $bdd->query($sql);
// Pour chaque équipe du classement, calcul du nombre de points
if ($req->rowCount() <> 0)
{
	while ($donnees = $req->fetch())
	{ 
		$classement++;
		if ($donnees['eqpChamp'] <> 0) { $classementC++; }

		if (isset($pointsClassement[$classement])) { $nbptsclass = $pointsClassement[$classement]; }
		else { $nbptsclass = 0; }
		if (isset($pointsClassement[$classementC])) { $nbptsclassC = $pointsClassement[$classementC]; }
		else { $nbptsclassC = 0; }
		
		$equipe = array("nomEquipe" => $donnees['nom'], "idEquipe" => $donnees['id_equipe'], "nbPts" => $nbptsclass, "nbPtsC" => $nbptsclassC, "nbCoups" => $donnees['points'], "eqpChamp" => $donnees['eqpChamp'], "logo" => $donnees['logo']);
		// fwrite($myfile, $donnees['nom'] . " nbPts: " . $nbptsclass . " nbPtsC: " . $nbptsclassC . " nbCoups : " . $donnees['points'] . " eqpChamp: " . $donnees['eqpChamp'] . chr(10));
		$classFinal[$classement] = $equipe;
	}
	
	// Calcul des égalités
	// Ce calcul est vrai uniquement parce que le tableau est classé par nombre de coups
	// Pour chaque équipe suivant le classement
	
	// En attente validation Fédé
	
	if($l_modeCalculPoints == "repartition")
	{

		foreach ($classFinal as $unClassement)
		{		
			/* Si le nombre de coup est identique, on stocke les points et le nombre d'équipes à égalité */
			if ($unClassement['nbCoups'] == $nbCoupsPrec)
			{ 
				$nbPtsRepart += $nbPtsPrec; 
				$nbEquipeEgal++; 
				if ($estEquipePrecC == true)
				{	$nbPtsRepartC += $nbPtsPrecC; 	$nbEquipeEgalC++; } 
				$estEgalite = true; 
			}
			else 
			{ 
				/* Si j'ai eu une égalité, je mets à jour les scores précédents */
				if ($estEgalite == true)
				{
					/* Je récupère les points de l'éuqipe précédent */
					$nbPtsRepart += $nbPtsPrec; $nbEquipeEgal++; 
					if ($estEquipePrecC == true) { $nbPtsRepartC += $nbPtsPrecC; $nbEquipeEgalC++; }
					
					/* Chaque équipe à égalité se réparti le nombre de points disponibles */
					foreach($classFinal as &$unClassementTmp) 
					{
						if ($unClassementTmp['nbCoups'] == $nbCoupsPrec)
						{ 	
							$unClassementTmp['nbPts'] = round($nbPtsRepart / $nbEquipeEgal);
							if ($unClassementTmp['eqpChamp'] <> 0)
							{ 	$unClassementTmp['nbPtsC'] = round($nbPtsRepartC / $nbEquipeEgalC);	}
							else 
							{ 	$unClassementTmp['nbPtsC'] = 0; }
	
						}
					}
					unset($unClassementTmp);
				}

				/* Réinitialisation des variables de calculs */
				if ($estEgalite == true) 
				{
					$nbPtsRepart = 0; $nbEquipeEgal = 0; $estEgalite = false;
					$nbPtsRepartC = 0; $nbEquipeEgalC = 0;
				}
			}

			/* prec */
			if ($unClassement['eqpChamp'] <> 0) 	{ 	$estEquipePrecC = true; } 	else { 	$estEquipePrecC = false;}
			$nbCoupsPrec  = $unClassement['nbCoups'];
			$nbPtsPrec    = $unClassement['nbPts'];
			$nbPtsPrecC   = $unClassement['nbPtsC'];
		}
	}
	else
	{
		foreach ($classFinal as &$unClassement)
		{		
			if ($unClassement['nbCoups'] == $nbCoupsPrec)
			{ $unClassement['nbPts'] = $nbPtsPrec; }

			if ($unClassement['eqpChamp'] <> 0)
			{		
				if ($unClassement['nbCoups'] == $nbCoupsPrecC)
				{ $unClassement['nbPtsC'] = $nbPtsPrecC; }
				$nbCoupsPrecC  = $unClassement['nbCoups'];
				$nbPtsPrecC    = $unClassement['nbPtsC'];
			}
		
			$nbCoupsPrec  = $unClassement['nbCoups'];
			$nbPtsPrec    = $unClassement['nbPts'];
		}
	}
	
	unset($unClassement);
		
	// Pour chaque compétiteur suivant le classement 
	foreach ($classFinal as $unClassement)
	{
		// Résultat pour affichage
		$lstClass .= '{"joueur":"' . $unClassement['nomEquipe'] . '","points":"' . $unClassement['nbPts'] . '","logo":"' . $unClassement['logo'] . '"},' ; 
		
		$l_resultat = $unClassement['nbCoups'] - $l_nbJoueurParEquipe * $l_parTotal; 
		
		// Enregistrement en table
		$insClass .= '("' . $obj->idComp . '", "2", "' . $unClassement['idEquipe'] . '", "' . $unClassement['nbPts'] . '", "' . $unClassement['nbPtsC'] . '", "' . $l_resultat .  '", "' . $unClassement['nbCoups'] . '"),';
	}
		
	$lstClass = substr($lstClass,0,strlen($lstClass)-1);
	$lstClass = "[" . $lstClass ."]";

	$insClass = substr($insClass,0,strlen($insClass)-1);
}
else { $lstClass = ''; $insClass = ''; }


// Enregistrement du nouveau classement
if ($insClass != '')
{
	$sql = 'INSERT INTO `classement_comp` (`id_comp`, `id_catClass`, `id_cat`, `score`, `scoreChamp`, `resultat`, `nbCoups`) VALUES ' . $insClass . ';';
	$reqIns = $bdd->query($sql);
	$reqIns->closeCursor();
}
// fclose($myfile);
echo $lstClass;
?>