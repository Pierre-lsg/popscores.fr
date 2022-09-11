<?php include("../../php/before.php");  ?>
<?php include("commun/before.php");  ?>

<!DOCTYPE html>
<html>
	<title>Ajouter nouveau joueur</title>
	<?php include("../../php/header.php");  ?>

	<body> 
		<?php include("../../php/connectdb.php");  ?>
		<?php include("commun/menuEtape.php");  ?>

		<!-- !PAGE CONTENT! -->
		<?php
			if (isset($_SESSION['id_champ'])) {$l_idChamp = $_SESSION['id_champ']; }
			else {$l_idChamp = '5';} 
		?>
		

		<!-- Champs cachés -->
		<input style="visibility:hidden" id="idChamp" value="<?php echo $l_idChamp; ?>">

		<div class="w3-main" style="margin-left:340px;margin-right:40px">

			<div style="text-align:center;margin-top:80px">
				<h1 class="w3-xxxlarge ps-text-color">Ajouter nouveau joueur</h1>

				<input style="visibility:hidden" id="test" value="<?php echo $_GET["id_comp"]; ?>">
				
				<form>
					<div class="w3-section">
						<table class="w3-table">
							<tr>
								<td width="50px">Prénom</td>
								<td><input id="prenomJ" class="w3-input w3-border ps-prenom" type="text" name="prenomJ" onBlur="activerValider()" required></td>
							</tr>
							<tr>
								<td width="50px">Nom</td>
								<td><input id="nomJ" class="w3-input w3-border ps-nom" type="text" name="nomJ" onBlur="activerValider()" required></td>
							</tr>
							<tr>
								<td width="50px">Equipe</td>
								<td>
								
								<!-- Liste des équipes -->
								<select class="w3-select" name="equipe" id="equipe" onBlur="activerValider()" required>
									<?php
									$reponse = $bdd->query('SELECT id_equipe, nom FROM equipe ORDER BY nom;');
									while ($lstEquipe = $reponse->fetch())
									{ echo '<option value="' . $lstEquipe['id_equipe'] . '">' . $lstEquipe['nom'] . '</option>' ;}
									$reponse->closeCursor();
									?>
								</select>

								
								</td>
							</tr>
						</table>
					</div>
					<button id="valider" onClick="creerJoueur();" type="button" class="w3-button w3-block w3-padding-large ps-color w3-margin-bottom" disabled>Valider</button>
				</form> 
				
				<p id="resultat">--</p>
				<div>
					<a class="ps-mini-lien-pop ps-color_sec" href="//site/v0/jec_gestion.php?id_comp=<?php echo $_GET['id_comp'] ?>">
						<span style="font-family: Calibri;">Retour</span>
					</a> 	
				</div>				

			</div>
		</div>
		
		<?php include("../../php/body_down.php");  ?>
		
			
		<script>
			// Activer le bouton 'Valider' si champs remplis
			function activerValider()
			{
				var l_prenom = '';
				var l_nom    = '';
				var	l_equipe = '';
				
				l_prenom = document.getElementById("prenomJ").value;
				l_nom 	 = document.getElementById("nomJ").value;
				l_equipe = document.getElementById("equipe").value;

				if (l_prenom.trim() != '' && l_nom.trim() != '' && l_equipe.trim() != '')
				{ 	document.getElementById("valider").disabled = false;
					document.getElementById("valider").focus();
				}
				else { document.getElementById("valider").disabled = true; }
				
			}
		
			// Créer un joueur
			function creerJoueur() 
			{
				var l_prenom, l_nom, l_equipe, l_champ;
				
				l_prenom = document.getElementById("prenomJ").value;
				l_nom 	 = document.getElementById("nomJ").value;
				l_equipe = document.getElementById("equipe").value;
				l_champ  = document.getElementById("idChamp").value;
				
				l_prenom = l_prenom.charAt(0).toUpperCase() + l_prenom.slice(1);;
				l_nom = l_nom.toUpperCase();
				
				var obj, dbParam, xmlhttp, myObj, x, txt = "";
				obj = { "prenom":l_prenom, "nom":l_nom, "equipe":l_equipe, "idchamp":l_champ};
				dbParam = JSON.stringify(obj);
				xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						document.getElementById("resultat").innerHTML = "Joueur " + l_prenom + " créé ...";
						// Réinitialisation des champs
						document.getElementById("prenomJ").value = "";
						document.getElementById("nomJ").value = "";
						document.getElementById("equipe").value = "";
						
						document.getElementById("prenomJ").focus();
						document.getElementById("valider").disabled = true;
					}
				};
				xmlhttp.open("POST", "//site/v0/json/creeJoueur.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send("x=" + dbParam);				
			}
		</script>
	</body>
</html>
