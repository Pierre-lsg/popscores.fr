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
$sqlSupp='DELETE FROM `classement_comp` WHERE id_comp='. $obj->idComp . ' AND id_catClass = 1;';
$reqSupp = $bdd->query($sqlSupp);
$reqSupp->closeCursor();

// Chargement de la table des scores
$pointsClassement = [];
$sqlCla='SELECT classement, points FROM `ref_classement` WHERE id_champ='. $obj->idChamp . ' AND id_catClass = 1;';
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


// Définition des variables locales
$classFinal   = array();
$competiteur  = '';
$nbptsclass   = 0 ;
$classement   = 0;
$nbptsclassC  = 0 ;
$classementC  = 0;
$nbCoupsPrec  = -1;
$nbCoupsPrecC = -1;
$nbPtsPrec    = 0;
$nbPtsRepart  = 0;
$nbPtsPrecC   = 0;
$nbPtsRepartC = 0;
$nbJoueurEgal = 0;
$nbJoueurEgalC 	= 0;
$estEgalite   	= false;
$estJoueurPrecC = false;

// ---------------------------------------------//
// -- Calcul du résultat pour la compétition -- //
// ---------------------------------------------//

// Pour chaque joueur du classement, calcul du nombre de points
$sql='SELECT j.id_joueur, CONCAT(j.prenom, " " ,j.nom) AS joueur, SUM(r.score) AS points, j.estCalculChampionnat as jouChamp FROM `resultat` r, joueur j WHERE r.id_comp = '. $obj->idComp . ' AND j.id_joueur = r.id_joueur GROUP BY(r.id_joueur) ORDER BY(SUM(r.score)), joueur ';
$req = $bdd->query($sql);
if ($req->rowCount() <> 0)
{
	// Récupération du classement 
	// Les égalités ne sont pas gérées
	while ($donnees = $req->fetch())
	{ 
		$classement++;
		if ($donnees['jouChamp'] <> 0) { $classementC++; }
		
		if (isset($pointsClassement[$classement])) { $nbptsclass = $pointsClassement[$classement]; }
		else { $nbptsclass = 0; }
		
		if (isset($pointsClassement[$classementC]) && $donnees['jouChamp'] <> 0) { $nbptsclassC = $pointsClassement[$classementC]; }
		else { $nbptsclassC = 0; }
		
		$competiteur = array("nomJoueur" => $donnees['joueur'], "idJoueur" => $donnees['id_joueur'], "nbPts" => $nbptsclass, "nbPtsC" => $nbptsclassC, "nbCoups" => $donnees['points'], "joueurChamp" => $donnees['jouChamp']);
		$classFinal[$classement] = $competiteur;

		// fwrite($myfile, $donnees['joueur'] . " nbPts: " . $nbptsclass . " nbPtsC: " . $nbptsclassC . " nbCoups: " . $donnees['points'] . " Champ :" . $donnees['jouChamp'] . chr(10));
	}

	// Calcul des égalités
	// Ce calcul est vrai uniquement parce que le tableau est classé par nombre de coups
	// Pour chaque compétiteur suivant le classement
	
	//-- En attente validation Fédé --//
	if($l_modeCalculPoints == "repartition")
	{
		foreach ($classFinal as $unClassement)
		{		
			/* Si le nombre de coup est identique, on stocke les points et le nombre de joueurs à égalités */
			if ($unClassement['nbCoups'] == $nbCoupsPrec)
			{ 
				$nbPtsRepart += $nbPtsPrec; 
				$nbJoueurEgal++; 
				if ($estJoueurPrecC == true)
				{	$nbPtsRepartC += $nbPtsPrecC; 	$nbJoueurEgalC++; } 
				$estEgalite = true; 
			}
			else 
			{ 
				/* Si j'ai eu une égalité, je mets à jour les scores précédents */
				if ($estEgalite == true)
				{
					/* Je récupère les points du joueur précédent */
					$nbPtsRepart += $nbPtsPrec; $nbJoueurEgal++; 
					if ($estJoueurPrecC == true) { $nbPtsRepartC += $nbPtsPrecC; $nbJoueurEgalC++; }
					
					/* Chaque joueur à égalité se réparti le nombre de points disponibles */
					foreach($classFinal as &$unClassementTmp) 
					{
						if ($unClassementTmp['nbCoups'] == $nbCoupsPrec)
						{ 	
							$unClassementTmp['nbPts'] = round($nbPtsRepart / $nbJoueurEgal);
							if ($unClassementTmp['joueurChamp'] <> 0)
							{ 	$unClassementTmp['nbPtsC'] = round($nbPtsRepartC / $nbJoueurEgalC);	}
							else 
							{ 	$unClassementTmp['nbPtsC'] = 0; }
	
						}
					}
					unset($unClassementTmp);
				}

				/* Réinitialisation des variables de calculs */
				if ($estEgalite == true) 
				{
					$nbPtsRepart = 0; $nbJoueurEgal = 0; $estEgalite = false;
					$nbPtsRepartC = 0; $nbJoueurEgalC = 0;
				}
			}

			/* prec */
			if ($unClassement['joueurChamp'] <> 0) 	{ 	$estJoueurPrecC = true; } 	else { 	$estJoueurPrecC = false;}
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
			
			if ($unClassement['jouChamp'] <> 0)
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
		$lstClass .= '{"joueur":"' . $unClassement['nomJoueur'] . '","points":"' . $unClassement['nbPts'] . '"},' ; 
		
		$l_resultat = $unClassement['nbCoups'] - $l_parTotal;
		// Enregistrement en table
		$insClass .= '("' . $obj->idComp . '", "1", "' . $unClassement['idJoueur'] . '", "' . $unClassement['nbPts'] . '", "' . $unClassement['nbPtsC'] . '", "' . $l_resultat .  '", "' . $unClassement['nbCoups'] . '"),';
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
echo $lstClass;
?>