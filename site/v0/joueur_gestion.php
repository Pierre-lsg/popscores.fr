<?php include("../../php/before.php");  ?>
<?php include("commun/before.php");  ?>

<!DOCTYPE html>
<html>
	<title>Gestion des joueurs</title>
	<?php include("../../php/header.php");  ?>

	<body> 
		<?php include("../../php/connectdb.php");  ?>
		<?php include("commun/menuEtape.php");  ?>

		<!-- !PAGE CONTENT! -->
		<div class="w3-main" style="margin-left:340px;margin-right:40px">

			<div style="text-align:center;margin-top:80px">
				<h1 class="w3-xxxlarge ps-text-color">Gestion des joueurs</h1>

				<!-- Champs cachés -->
				<!-- Identifiant de compétition -->
				<input style="visibility:hidden" id="idComp" value="<?php echo $_GET["id_comp"]; ?>">

				<?php
					if (isset($_GET['idClub'])) {$l_idClub = $_GET['idClub']; }
					else {$l_idClub = '0';}
					if (isset($_SESSION['id_champ'])) {$l_idChamp = $_SESSION['id_champ']; }
					else {$l_idChamp = '8';}					
				?>
				
				<div class="w3-section">
				
					<!-- Liste des clubs -->
					<select class="w3-select" name="club" id="club" onchange="changerClub()" required>
						<?php
						$cpt = 0; $l_idClubSlt = 0;
						$reponse = $bdd->query('SELECT id_club, nom FROM club WHERE id_club <> 0 ORDER BY nom;');
						while ($lstClub = $reponse->fetch())
						{ 
							if ($lstClub['id_club'] == $l_idClub)
							{
								echo '<option value="' . $lstClub['id_club'] . '" selected="selected">' . $lstClub['nom'] . '</option>' ;
							}
							else
							{	
								echo '<option value="' . $lstClub['id_club'] . '">' . $lstClub['nom'] . '</option>' ;
							}
							if ($cpt == 0)
							{ $l_idClubSlt = $lstClub['id_club']; $cpt++; }
						}
						$reponse->closeCursor();
						if ($l_idClub <> $l_idClubSlt) { $l_idClub == $l_idClubSlt; }
						?>
					</select>

					<br><br>
					
					<table class="w3-table">
						<?php
						$reponse = $bdd->query('SELECT id_joueur, prenom, nom, estCalculChampionnat FROM `joueur` WHERE id_club = ' . $l_idClub .' AND id_champ = ' . $l_idChamp . ' ORDER BY estCalculChampionnat DESC;');
						while ($lstJou = $reponse->fetch())
						{ 
							if($lstJou['estCalculChampionnat'] == 0) { $l_checked = '';}
							else { $l_checked = 'checked';}
							
							echo '<tr>
								<td width="200px">Prénom</td>
								<td><input id="nom" class="w3-input w3-border" type="text" name="prenomJ" value="' . $lstJou['prenom'] . '" onblur="modifiePrenomJoueur(this.value,' . $lstJou['id_joueur'] . ')" required></td>
							</tr>
							<tr>
								<td width="200px">Nom</td>
								<td><input id="nom" class="w3-input w3-border" type="text" name="nomJ" value="' . $lstJou['nom'] . '" onblur="modifieNomJoueur(this.value,' . $lstJou['id_joueur'] . ')" required></td>
							</tr>
							<tr>
								<td width="200px">Calcul Championnat</td>
								<td><input type="checkbox" onblur="modifieCalculJoueur(this.checked,' . $lstJou['id_joueur'] . ')" ' . $l_checked . '></td>
							</tr>
							<tr>
								<td> --------- </td><td></td>
							</tr>
							';		
						}
						$reponse->closeCursor();
						?>

					</table>
				</div>
				
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
			// modifier le prénom du joueur
			function modifiePrenomJoueur(i_prenom,i_idJoueur) 
			{
				var obj, dbParam, xmlhttp, myObj, x, txt = "";
				obj = {"prenom":i_prenom, "idJoueur":i_idJoueur};
				dbParam = JSON.stringify(obj);
				xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						document.getElementById("resultat").innerHTML = "Joueur modifiée ...";
					}
				};
				xmlhttp.open("POST", "//site/v0/json/modifiePrenomJoueur.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send("x=" + dbParam);			
			}
			
			// modifier le nom du joueur
			function modifieNomJoueur(i_nom,i_idJoueur) 
			{
				var obj, dbParam, xmlhttp, myObj, x, txt = "";
				obj = {"nom":i_nom, "idJoueur":i_idJoueur};
				dbParam = JSON.stringify(obj);
				xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						document.getElementById("resultat").innerHTML = "Joueur modifiée ...";
					}
				};
				xmlhttp.open("POST", "//site/v0/json/modifieNomJoueur.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send("x=" + dbParam);			
			}
			
			// modifier la prise en compte du joueur dans le calcul du championnat 
			function modifieCalculJoueur(i_checked,i_idJoueur) 
			{
				var l_checked = ((i_checked == true) ? '1' : '0');
				
				var obj, dbParam, xmlhttp, myObj, x, txt = "";
				obj = {"calcul":l_checked, "idJoueur":i_idJoueur};
				dbParam = JSON.stringify(obj);
				xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						document.getElementById("resultat").innerHTML = "Joueur modifiée ...";
					}
				};
				xmlhttp.open("POST", "//site/v0/json/modifieCalculJoueur.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send("x=" + dbParam);
			}
			
			function changerClub()
			{
				var l_idClub = document.getElementById("club").value;
				var link = "//site/v0/joueur_gestion.php?id_comp="+document.getElementById("idComp").value+"&idClub="+l_idClub;
				
				open(link,"_self");
			}

		</script>
	</body>
</html>
