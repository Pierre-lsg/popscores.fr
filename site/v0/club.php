<?php include("../../php/before.php");  ?>
<?php include("commun/before.php");  ?>

<!DOCTYPE html>
<html>
	<title>Ajouter nouveau club</title>
	<?php include("../../php/header.php");  ?>

	<body> 
		<?php include("../../php/connectdb.php");  ?>
		<?php include("commun/menuEtape.php");  ?>

		<!-- !PAGE CONTENT! -->
		<div class="w3-main" style="margin-left:340px;margin-right:40px">

			<div style="text-align:center;margin-top:80px">
				<h1 class="w3-xxxlarge ps-text-color">Ajouter nouveau club</h1>

				<input style="visibility:hidden" id="test" value="<?php echo $_GET["id_comp"]; ?>">
				
				<form>
					<div class="w3-section">
						<table class="w3-table">
							<tr>
								<td width="50px">Nom</td>
								<td><input id="nom" class="w3-input w3-border" type="text" name="nomC" onBlur="activerValider()" required></td>
							</tr>
							<tr>
								<td width="50px">Descriptif</td>
								<td><textarea id="descriptif" class="w3-textarea" name="descriptif" rows ="4" onBlur="activerValider()" required>Description du club ...</textarea></td>
							</tr>
						</table>
					</div>
					<button id="valider" onClick="creerEquipe();" type="button" class="w3-button w3-block w3-padding-large ps-color w3-margin-bottom" disabled>Valider</button>
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
				var l_nom   	   = '';
				var	l_descriptif   = '';
				
				l_nom 	     = document.getElementById("nom").value;
				l_descriptif = document.getElementById("descriptif").value;

				if (l_nom.trim() != '' && l_descriptif.trim() != '')
				{ 	document.getElementById("valider").disabled = false;
					document.getElementById("valider").focus();
				}
				else { document.getElementById("valider").disabled = true; }
				
			}
		
			// Créer un club
			function creerEquipe() 
			{
				var nom, l_logo, l_descriptif;
				
				l_nom        = document.getElementById("nom").value;
				l_logo       = 'defaut_mini.jpg';
				l_descriptif = document.getElementById("descriptif").value;
				
				var obj, dbParam, xmlhttp, myObj, x, txt = "";
				obj = {"nom":l_nom, "logo":l_logo, "descriptif":l_descriptif};
				dbParam = JSON.stringify(obj);
				xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						document.getElementById("resultat").innerHTML = "Club " + l_nom + " créé ...";
						// Réinitialisation des champs
						document.getElementById("nom").value = "";
						document.getElementById("descriptif").value = "";
						
						document.getElementById("nom").focus();
						document.getElementById("valider").disabled = true;
					}
				};
				xmlhttp.open("POST", "//site/v0/json/creeClub.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send("x=" + dbParam);				
			}
		</script>
	</body>
</html>
