<?php include("../../php/before.php");  ?>
<?php include("commun/before.php");  ?>

<!DOCTYPE html>
<html>
	<title>Gestion Etape/Parcours</title>
	<?php include("../../php/header.php");  ?>

	<body> 
		<?php include("../../php/connectdb.php");  ?>
		<?php include("commun/menuEtape.php");  ?>

		<!-- !PAGE CONTENT! -->
		<div class="w3-main" style="margin-left:340px;margin-right:40px">

			<div style="text-align:center;margin-top:80px">
				<!-- <h1 class="w3-xxxlarge ps-text-color">Gestion Etape/Parcours</h1> -->
				<?php 
					$reponse = $bdd->query('SELECT c.nom, c.saison, c.saison, e.nom as nom_comp, e.etape, e.id_comp, e.nbJouParEqp, e.nbEqpParFly, e.dateC, e.dateResultat, DATE_FORMAT(e.dateC,"%d/%m") AS dateComp FROM championnat c, competition e WHERE e.id_comp = ' . $_GET["id_comp"] . ' AND c.id_champ = e.id_champ');
					while ($donnees = $reponse->fetch())
					{ 
						echo '<h1 class="w3-xxxlarge ps-text-color">' . $donnees['nom'] . " " . $donnees['saison'] . " <br> #" . $donnees['etape'] . " " . $donnees['nom_comp'] . " - " . $donnees['dateComp'] . '</h1>'; 
						$nbJouParEqp = $donnees['nbJouParEqp'];
						$nbEqpParFly = $donnees['nbEqpParFly'];
						$dateC = $donnees['dateC'];
						$dateResultat = $donnees['dateResultat'];
					}
					$reponse->closeCursor();
				?>

				<input style="visibility:hidden" id="idComp" value="<?php echo $_GET["id_comp"]; ?>">
				
				<table class="w3-table">
					<tr>
						<td width="100px">Nb joueur par équipe</td>
						<td><input id="nbJouParEqp" class="w3-input w3-border" type="number" min="1" max="999" name="nbJouParEqp" value="<?php echo $nbJouParEqp ?>" required></td>
					</tr>
					<tr>
						<td width="100px">Nb équipe par fly</td>
						<td><input id="nbEqpParFly" class="w3-input w3-border" type="number" min="1" max="999" name="nbEqpParFly" value="<?php echo $nbEqpParFly ?>" required></td>
					</tr>
					<tr>
						<td width="100px">Date de la compétition</td>
						<td><input id="dateC" class="w3-input w3-border" type="date"  name="dateC" value="<?php echo $dateC ?>" required></td>
					</tr>
					<tr>
						<td width="100px">Date du résultat</td>
						<td><input id="dateResultat" class="w3-input w3-border" type="date" name="dateResultat" value="<?php echo $dateResultat ?>" required></td>
					</tr>
					<tr>
						<td width="100px">Arbitres : </td>
						<td><?php
							 $rep = $bdd->query('SELECT a.nom FROM arbitre a, arbitre_competition c WHERE c.id_comp = ' . $_GET['id_comp'] .' AND c.id_arbitre = a.id_arbitre	');
							 while ($arbitre = $rep->fetch())
							 {
								echo $arbitre['nom'] . '<br>';
							 }
							 $rep->closeCursor();							 
						?>
						<a href="/site/v0/arbitre_comp.php"><button class="w3-button w3-block w3-padding-small ps-color w3-margin-bottom">Ajouter arbitre</button></a>
						</td>
					</tr>
				</table>
				
				<table class="w3-table">
					
					<!-- Détail de l'étape -->
					<?php 
					$reponse = $bdd->query('SELECT c.id_champ, c.nom, c.saison, e.nom as nom_comp, e.etape, e.id_comp FROM championnat c, competition e WHERE e.id_comp = ' . $_GET["id_comp"] . ' AND c.id_champ = e.id_champ');
					$detEtape = $reponse->fetch(); 
					$reponse->closeCursor();
					?>
				</table>
				<br><br>
				
				
				<br><br>
				<button onClick="stockeParametres();" type="submit" class="w3-button w3-block w3-padding-large ps-color w3-margin-bottom">Valider</button>
				
				<p id="resultat">--</p>

			</div>
		</div>
		
		<?php include("../../php/body_down.php");  ?>
		
			
		<script>		
			// Enregistre le parcours saisi
			function stockeParametres() {
				document.getElementById("resultat").innerHTML = "dde enreg";
				var l_nbJouParEqp, l_nbEqpParFly, l_dateC, l_dateResultat, l_idComp ;
				
				l_idComp = document.getElementById("idComp").value;

				l_nbJouParEqp = document.getElementById("nbJouParEqp").value;
				l_nbEqpParFly = document.getElementById("nbEqpParFly").value;
				l_dateC = document.getElementById("dateC").value;
				l_dateResultat = document.getElementById("dateResultat").value;

				// Récupération des détails du trou dans la base sgc
				var obj, dbParam, xmlhttp, myObj, x, txt = "";
				obj = { "nbJouParEqp":l_nbJouParEqp, "nbEqpParFly":l_nbEqpParFly, "dateC":l_dateC, "dateResultat":l_dateResultat, "id_comp":l_idComp};
				dbParam = JSON.stringify(obj);
				xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						document.getElementById("resultat").innerHTML = "OK"
						// enregistrement mis à jour
					}
				};
				xmlhttp.open("POST", "//site/v0/json/enregParametres.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send("x=" + dbParam);				
			}
		</script>
	</body>
</html>
