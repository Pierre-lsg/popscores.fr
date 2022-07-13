<?php

// $myfile = fopen("trace_json.txt", "w") or die("Unable to open file!");

include '../php/connectdb.php';

header("Content-Type: application/json; charset=UTF-8");
$obj = json_decode($_POST["x"], false);
$lstClass = '';
$insClass = '';

// Suppression du classement précédent
$sqlSupp='DELETE FROM `classement_champ` WHERE id_champ='. $obj->idChamp . ' AND id_catClass = 3;';
$reqSupp = $bdd->query($sqlSupp);
$reqSupp->closeCursor();


// Pour chaque joueur du classement, calcul du nombre de points
// Création de l'enregistrement 'classement_champ'
$classement   = 0;
$numEnreg     = 0;
$nbPointsPrec = 0;

// Calcul des points du club
// Pour chaque club engagé dans le championnat
// TODO : optimiser la règle. Actuellement on teste tous les clubs connus
$sqlCec='SELECT id_club FROM `club` WHERE id_club <> 0 ORDER BY id_club ASC';
$reqCec = $bdd->query($sqlCec);
while ($lstClub = $reqCec->fetch())
{ 
	$nbPointsClub = 0;
	// Pour chaque compétition où le club a engagé au moins une équipe
	// TODO : optimiser la règle. Actuellement, pour chaque compétition du championnat
	$sqlCpC='SELECT `id_comp` FROM `competition` WHERE `id_champ` = '. $obj->idChamp . '';
	$reqCpc = $bdd->query($sqlCpC);
	while ($lstComp = $reqCpc->fetch())
	{ 
		// Récupérer le nombre de points marqués par sa meilleure équipe 
		$sqlBsc='SELECT MAX(ccc.scoreChamp) AS points FROM championnat c, competition cc, classement_comp ccc, equipe e, club cl WHERE c.id_champ = '. $obj->idChamp . ' AND cc.id_comp = ' . $lstComp['id_comp'] . ' AND cc.pourChampionnat <> 0 AND cl.id_club = ' . $lstClub['id_club'] . ' AND c.id_champ = cc.id_champ AND cc.id_comp = ccc.id_comp AND ccc.id_catClass = 2 AND ccc.id_cat = e.id_equipe AND cl.id_club = e.id_club AND e.estCalculChampionnat = 1 GROUP BY e.id_club ORDER BY MAX(ccc.scoreChamp) DESC ';
		$reqBsc = $bdd->query($sqlBsc);
		while ($BestScore = $reqBsc->fetch())
		{ 
			$nbPointsClub = $nbPointsClub + $BestScore['points']; 
		}			
		$reqBsc->closeCursor();

	}
	$reqCpc->closeCursor();
	
	// Enregistrement en table
	if ($nbPointsClub != 0)
	{ $insClass .= '("' . $obj->idChamp . '", "3", "' . $lstClub['id_club'] . '", "' . $nbPointsClub . '"),'; }
}
$insClass = substr($insClass,0,strlen($insClass)-1);
$reqCec->closeCursor();


// Enregistrement du nouveau classement
if ($insClass != '')
{
	$sql = 'INSERT INTO `classement_champ` (`id_champ`, `id_catClass`, `id_cat`, `score`) VALUES ' . $insClass . ';';
	$reqIns = $bdd->query($sql);
	$reqIns->closeCursor();
}

// Calcul du résultat à afficher
$sql='SELECT cl.id_club, cl.logo, cl.nom AS joueur, c.score AS points FROM classement_champ c, club cl WHERE c.id_champ = '. $obj->idChamp . ' AND c.id_catClass = 3 AND c.id_cat = cl.id_club ORDER BY c.score DESC, cl.nom ';
// fwrite($myfile, $sql);
// fclose($myfile);
$req = $bdd->query($sql);
if ($req->rowCount() <> 0)
{
	while ($donnees = $req->fetch())
	{ $lstClass .= '{"joueur":"' . $donnees['joueur'] . '","points":"' . $donnees['points'] . '","logo":"' . $donnees['logo'] . '"},' ; }

	$lstClass = substr($lstClass,0,strlen($lstClass)-1);
	$lstClass = "[" . $lstClass ."]";
}
$req->closeCursor();
echo $lstClass;
	
?>