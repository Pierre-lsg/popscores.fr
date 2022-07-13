<?php	
	session_start();
	
	include("php/connectdb.php"); 

	
	$l_idChamp = '';
	$l_idComp = '';			
	/* Calcul de la compétition en cours */
	$sqlC = 'SELECT `id_comp`, `id_champ` FROM `competition` WHERE `dateC` = CURDATE() ;';
	$reponseC = $bdd->query($sqlC);
	if ($reponseC->rowCount() <> 0)
	{
		$donneesC = $reponseC->fetch();
		$l_idChamp = $donneesC['id_champ'];
		$l_idComp = $donneesC['id_comp'];			
	}
	$reponseC->closeCursor();	

	/* Si l'ID et mot de passe sont corrects 
	   autoriser l'accès au site de saisie des scores de l'arbitre */
	if (isset($_POST['ident']) and isset($_POST['mdp']))
	{
		$login = preg_replace("#[^a-zA-Z0-9]#", "", $_POST['ident']);
		$mdp = preg_replace("#[^a-zA-Z0-9]#", "", $_POST['mdp']);
		$sql = 'SELECT id_arbitre FROM `arbitre` WHERE id_arbitre="'.$login.'" and code="'.$mdp.'";';
		$reponse = $bdd->query($sql);
		if ($reponse->rowCount() <> 0)
		{
			$_SESSION['connexion'] = 'oui';
			$_SESSION['id_champ'] = $l_idChamp;
			$_SESSION['id_comp'] = $l_idComp;			
			/* Stockage de id_org */
			$donnees = $reponse->fetch();
			$_SESSION['id_arbitre'] = $donnees['id_arbitre'];
			
		}
		$reponse->closeCursor();
		
		$sql = 'SELECT numero FROM surveillance_trou s, trou t WHERE s.id_arbitre="'.$login.'" AND s.id_comp="13" AND s.id_trou = t.id_trou ;';
		$reponse = $bdd->query($sql);
		if ($reponse->rowCount() <> 0)
		{
			$donnees = $reponse->fetch();
			$_SESSION['numTrou'] = $donnees['numero'];			
		}
		$reponse->closeCursor();
	}
	
	/* Si l'arbitre s'est déjà authentifié 
	   Charger la page d'accueil d'arbitre de la compétition */
	if (isset($_SESSION['connexion']))
	{
		if ($_SESSION['connexion'] == 'oui')
		{
			header('Location: site/arb_v0/saisirScoresArbitre_trou.php');
			exit;
		}
	}
	else 
	{
		$_SESSION['connexion'] = 'non';
	}
?>

<!DOCTYPE html>
<html>
	<head>
	</head>
	<title>Accueil Pop Scores</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/css/w3.css">
	<style>		
		body {
			background-image : url("logo.jpg");
			background-size : cover;
		}
	</style>
	
	<body>
		<?php 
			if ($l_idComp == '')
			{ echo "<p> Pas de compétition en cours ...</p>"; }
		?>
		<form action="accueilArbitre.php" method="post">
			<div class="w3-section">
				<table class="w3-table">
					<tr>
						<td width="150px">Identifiant </td>
						<td>
							<!-- Liste des équipes -->
							<select class="w3-select" name="ident" id="ident" required>
								<?php
								$reponse = $bdd->query('SELECT a.id_arbitre, a.nom FROM `arbitre` a, `arbitre_competition` ac, `competition` c WHERE c.dateC = CURDATE() AND ac.id_comp = c.id_comp AND ac.id_arbitre = a.id_arbitre ;');
								while ($lstArb = $reponse->fetch())
								{ 
									echo '<option value="' . $lstArb['id_arbitre'] . '">' . $lstArb['nom'] . '</option>' ;
								}
								$reponse->closeCursor();
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td width="150px">Mot de passe</td>
						<td><input id="mdp" class="w3-input w3-border" type="number" min="1000" max="9999" name="mdp" required></td>
					</tr>
				</table>
				<button id="valider" type="submit" class="w3-button w3-block w3-padding-large ps-color_sec w3-margin-bottom" value="Se connecter">Se connecter</button>
			</div>
		</form> 
		
	</body>
</html>
