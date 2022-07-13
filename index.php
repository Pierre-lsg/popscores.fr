<?php
	session_start();
	
	include("php/connectdb.php"); 

	/* Si login et mot de passe sont corrects 
	   autoriser l'accès au site */
	if (isset($_POST['ident']) and isset($_POST['mdp']))
	{
		$login = preg_replace("#[^a-zA-Z0-9]#", "", $_POST['ident']);
		$mdp = preg_replace("#[^a-zA-Z0-9]#", "", $_POST['mdp']);
		$sql = 'SELECT id_org FROM `organisateur` WHERE pseudo="'.$login.'" and mdp="'.$mdp.'";';
		$reponse = $bdd->query($sql);
		if ($reponse->rowCount() <> 0)
		{
			$l_idChamp = '5';
			$l_estAnimation = 1;
			
			$_SESSION['connexion'] = 'oui';
			/* Stockage de id_org */
			$donnees = $reponse->fetch();
			$_SESSION['id_org'] = $donnees['id_org'];
			
			/* Liste des championnats gérés par l'organisateur */
			$sqlC = 'SELECT DISTINCT(c.id_champ) AS idChamp FROM `competition` c, `organisation_comp` o WHERE id_org = "'.$donnees['id_org'].'" AND o.id_comp = c.id_comp ORDER BY c.id_champ DESC;';
			$reponseC = $bdd->query($sqlC);
			if ($reponseC->rowCount() <> 0)
			{
				$donneesC = $reponseC->fetch();
				$l_idChamp = $donneesC['idChamp'];
			}
			$reponseC->closeCursor();

			$_SESSION['id_champ'] = $l_idChamp;
			
			/* Le championnat est-il une initiation */
			$sqlI = 'SELECT `animation` FROM `championnat` WHERE `id_champ` = '.$l_idChamp.';';
			echo $sqlI;
			$reponseI = $bdd->query($sqlI);
			if ($reponseI->rowCount() <> 0)
			{
				$donneesI = $reponseI->fetch();
				$l_estAnimation = $donneesI['animation'];
			}
			$reponseI->closeCursor();
		}
		$reponse->closeCursor();
	}
	
	/* Si l'utilisateur s'est déjà authentifié 
	   Charger la page d'accueil du FCPT 2018 */
	if (isset($_SESSION['connexion']))
	{
		if ($_SESSION['connexion'] == 'oui')
		{
			if ($l_estAnimation <> 0)
			{	header('Location: site/animation/index.php');	}
			else 
			{	header('Location: site/v0/index.php');	}
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
		<!-- Matomo -->
		<script type="text/javascript">
		  var _paq = _paq || [];
		  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
		  _paq.push(['trackPageView']);
		  _paq.push(['enableLinkTracking']);
		  (function() {
			var u="//www.popscores.fr/analytics/";
			_paq.push(['setTrackerUrl', u+'piwik.php']);
			_paq.push(['setSiteId', '1']);
			var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
			g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
		  })();
		</script>
		<!-- End Matomo Code -->
	</head>
	<title>Accueil Pop Scores</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="/~popscores/css/w3.css">
	<style>		
		body {
			background-image : url("logo.jpg");
			background-size : cover;
		}
	</style>
	
	<body>
		<form action="index.php" method="post">
			<div class="w3-section">
				<table class="w3-table">
					<tr>
						<td width="150px">Identifiant </td>
						<td><input id="ident" class="w3-input w3-border" type="text" name="ident" required></td>
					</tr>
					<tr>
						<td width="150px">Mot de passe</td>
						<td><input id="mdp" class="w3-input w3-border" type="password" name="mdp" required></td>
					</tr>
				</table>
				<button id="valider" type="submit" class="w3-button w3-block w3-padding-large ps-color_sec w3-margin-bottom" value="Se connecter">Se connecter</button>
			</div>
		</form> 
		
	</body>
</html>
