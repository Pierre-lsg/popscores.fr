<?php include("../../php/before.php");  ?>
<?php include("commun/before.php");  ?>

<!DOCTYPE html>
<html>
	<title>Ajouter nouvelle équipe</title>
	<?php include("../../php/header.php");  ?>

	<body> 
		<?php include("../../php/connectdb.php");  ?>
		<?php include("commun/menuEtape.php");  ?>

		<!-- !PAGE CONTENT! -->
		<div class="w3-main" style="margin-left:340px;margin-right:40px">

			<div style="text-align:center;margin-top:80px">
				<h1 class="w3-xxxlarge ps-text-color">Ajouter nouvelle équipe</h1>

				<input style="visibility:hidden" id="test" value="<?php echo $_GET["id_comp"]; ?>">
				
				<form>
					<div class="w3-section">
						<table class="w3-table">
							<tr>
								<td width="50px">Nom</td>
								<td><input id="nom" class="w3-input w3-border" type="text" name="nomJ" onBlur="activerValider()" required></td>
							</tr>
							<tr>
								<td width="50px">Club</td>
								<td>
								
								<!-- Liste des championnats -->
								<select class="w3-select" name="club" id="club" onBlur="activerValider()" required>
									<?php
									$reponse = $bdd->query('SELECT id_club, nom FROM club ORDER BY nom;');
									while ($lstClub = $reponse->fetch())
									{ echo '<option value="' . $lstClub['id_club'] . '">' . $lstClub['nom'] . '</option>' ;}
									$reponse->closeCursor();
									?>
								</select>

								
								</td>
							</tr>
						</table>
					</div>
					<button id="valider" onClick="creerEquipe();" type="button" class="w3-button w3-block w3-padding-large ps-color w3-margin-bottom" disabled>Valider</button>
				</form> 
				
				<p id="resultat">--</p>

				<div>
					<a class="ps-mini-lien-pop ps-color_sec" href="/~popscores/site/v0/jec_gestion.php?id_comp=<?php echo $_GET['id_comp'] ?>">
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
				var l_nom    = '';
				var	l_club   = '';
				
				l_nom 	 = document.getElementById("nom").value;
				l_club   = document.getElementById("club").value;

				if (l_nom.trim() != '' && l_club.trim() != '')
				{ 	document.getElementById("valider").disabled = false;
					document.getElementById("valider").focus();
				}
				else { document.getElementById("valider").disabled = true; }
				
			}
		
			// Créer un joueur
			function creerEquipe() 
			{
				var l_prenom, l_nom, l_club;
				
				l_nom 	 = document.getElementById("nom").value;
				l_club = document.getElementById("club").value;
				
				var obj, dbParam, xmlhttp, myObj, x, txt = "";
				obj = {"nom":l_nom, "club":l_club};
				dbParam = JSON.stringify(obj);
				xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						document.getElementById("resultat").innerHTML = "Equipe " + l_nom + " créé ...";
						// Réinitialisation des champs
						document.getElementById("nom").value = "";
						document.getElementById("club").value = "";
						
						document.getElementById("nom").focus();
						document.getElementById("valider").disabled = true;
					}
				};
				xmlhttp.open("POST", "/~popscores/site/v0/json/creeEquipe.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send("x=" + dbParam);				
			}
		</script>
	</body>
</html>
