<?php

// $myfile = fopen("trace_json.txt", "w") or die("Unable to open file!");
// fwrite($myfile, $sqlPar);
// fclose($myfile);

include '../php/connectdb.php';

header("Content-Type: application/json; charset=UTF-8");
$obj = json_decode($_POST["x"], false);
$lstClass = '';
$insClass = '';

// Suppression du classement précédent
$sqlSupp='DELETE FROM `classement_champ` WHERE id_champ='. $obj->idChamp . ' AND id_catClass = 1;';
$reqSupp = $bdd->query($sqlSupp);
$reqSupp->closeCursor();

// Paramètres du championnat
// Nombre de compétition prises en compte dans le calcul ; Règles FFSG 2023
$l_nbCompCalcul = 0;
$l_rglFssg2023 = false;
$sqlPar = 'SELECT `nbCompCalcul`, `FFSG2023` FROM `championnat` WHERE id_champ = ' . $obj->idChamp . ' ;';
$reqPar = $bdd->query($sqlPar);
if ($reqPar->rowCount() <> 0)
{ 
	$res = $reqPar->fetch(); 
	$l_nbCompCalcul = $res['nbCompCalcul'];
	if ($res['FFSG2023'] <> 0) { $l_rglFssg2023 = true; }
}
$reqPar->closeCursor();
if ($l_nbCompCalcul == 0) { $l_nbCompCalcul = 999; } 

// Pour chaque joueur du classement, calcul du nombre de points
// Création de l'enregistrement 'classement_champ'
$nbComp       = 0;
$nbPointsJ    = 0;
$l_idJoueur   = 0;

if ($l_rglFssg2023 == true)
{	$sql='SELECT j.id_joueur, CONCAT(j.prenom, " " ,j.nom) AS joueur, ccc.scoreChamp AS points FROM championnat c, competition cc, classement_comp ccc, joueur j WHERE c.id_champ = '. $obj->idChamp .' AND c.id_champ = cc.id_champ AND cc.pourChampionnat <> 0 AND ccc.id_catClass = 1 AND ccc.id_comp = cc.id_comp AND j.id_joueur = ccc.id_cat AND j.estCalculChampionnat <> 0 ORDER BY j.id_joueur, points DESC'; }
else {	$sql='SELECT j.id_joueur, CONCAT(j.prenom, " " ,j.nom) AS joueur, ccc.scoreChamp AS points FROM championnat c, competition cc, classement_comp ccc, joueur j WHERE c.id_champ = '. $obj->idChamp .' AND c.id_champ = cc.id_champ AND cc.pourChampionnat <> 0 AND cc.individuel <> 0 AND ccc.id_catClass = 1 AND ccc.id_comp = cc.id_comp AND j.id_joueur = ccc.id_cat AND j.estCalculChampionnat <> 0 ORDER BY j.id_joueur, points DESC'; }
$req = $bdd->query($sql);
if ($req->rowCount() <> 0)
{
	while ($donnees = $req->fetch())
	{ 
		// Si calcul classement d'un autre joueur

		if ($l_idJoueur != $donnees['id_joueur'])
		{
			// Enregistrement du joueur précédent dans les listes
			if ($l_idJoueur != 0)
			{
				$lstClass .= '{"joueur":"' . $donnees['joueur'] . '","points":"' . $nbPointsJ . '"},' ; 		
				$insClass .= '("' . $obj->idChamp . '", "1", "' . $l_idJoueur . '", "' . $nbPointsJ . '"),';
			}
			
			// Réinitialisation des variables pour le joueur suivant
			$nbComp      = 1;
			$nbPointsJ   = $donnees['points'];
			$l_idJoueur  = $donnees['id_joueur'];
		}
		else 
		{
			$nbComp++;
			if ($nbComp <= $l_nbCompCalcul)
			{
				$nbPointsJ = $nbPointsJ + $donnees['points'];
			}
		}
	}
	// Enregistrement du dernier joueur
	$lstClass .= '{"joueur":"' . $l_idJoueur . '","points":"' . $nbPointsJ . '"},' ; 		
	$insClass .= '("' . $obj->idChamp . '", "1", "' . $l_idJoueur . '", "' . $nbPointsJ . '"),';

	// Nettoyage des listes pour traitement
	$lstClass = substr($lstClass,0,strlen($lstClass)-1);
	$lstClass = "[" . $lstClass ."]";

	$insClass = substr($insClass,0,strlen($insClass)-1);
}
else { $lstClass = ''; $insClass = ''; }


// Enregistrement du nouveau classement
if ($insClass != '')
{
	$sql = 'INSERT INTO `classement_champ` (`id_champ`, `id_catClass`, `id_cat`, `score`) VALUES ' . $insClass . ';';
	$reqIns = $bdd->query($sql);
	$reqIns->closeCursor();
}
	
echo $lstClass;
?>
