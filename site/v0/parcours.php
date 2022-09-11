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
					$reponse = $bdd->query('SELECT c.nom, c.saison, c.saison, e.nom as nom_comp, e.etape, e.id_comp, e.nbTrou, DATE_FORMAT(e.dateC,"%d/%m") AS dateC FROM championnat c, competition e WHERE e.id_comp = ' . $_GET["id_comp"] . ' AND c.id_champ = e.id_champ');
					while ($donnees = $reponse->fetch())
					{ 
						echo '<h1 class="w3-xxxlarge ps-text-color">' . $donnees['nom'] . " " . $donnees['saison'] . " <br> #" . $donnees['etape'] . " " . $donnees['nom_comp'] . " - " . $donnees['dateC'] . '</h1>'; 
						$nbTrou = $donnees['nbTrou'];
					}
					$reponse->closeCursor();
				?>

				<input style="visibility:hidden" id="idComp" value="<?php echo $_GET["id_comp"]; ?>">
				
				<table class="w3-table">
					<tr>
						<td width="100px">Nb Trou</td>
						<td><input id="nbTrou" class="w3-input w3-border" type="number" min="0" max="999"  name="nbTrou" onBlur="recalculParcours()" value="<?php echo $nbTrou ?>" required></td>
					</tr>
				</table>
				
				<table class="w3-table">
					
					<!-- Détail de l'étape -->
					<?php 
					$reponse = $bdd->query('SELECT c.id_champ, c.nom, c.saison, e.nom as nom_comp, e.etape, e.id_comp FROM championnat c, competition e WHERE e.id_comp = ' . $_GET["id_comp"] . ' AND c.id_champ = e.id_champ');
					$detEtape = $reponse->fetch(); 
					$reponse->closeCursor();
					?>


					
					<!-- Détail du parcours -->
					<?php 
					$parTrou = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
					$fjTrou  = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
					$reponse = $bdd->query('SELECT numero, par, id_formjeu FROM trou WHERE id_comp = ' . $_GET["id_comp"] . ' ORDER BY numero');
					// echo $reponse->rowCount();
					while ($detParcours = $reponse->fetch())
					{ 	$parTrou[$detParcours['numero']] = $detParcours['par']; 
						$fjTrou[$detParcours['numero']]  = $detParcours['id_formjeu']; }
					$reponse->closeCursor();
					// echo $parTrou[0];
					?>
				</table>
				<br><br>
				
				<!-- Détail du parcours -->
				<table class="w3-table" id="parcours" >
					<tr class="ps-color_sec">
						<td>Trou</td>
						<?php for($i = 1; $i <= $nbTrou; $i++) { echo '<td>'.$i.'</td>'; } ?>
					</tr>
					<tr>
						<td class="ps-color_sec">Par</td>
						<?php 
							for($i = 1; $i <= $nbTrou; $i++) 
								{ echo '<td><input type="number" min="0" max="999" id="trou' . $i . '" value="' . $parTrou[$i] . '" size="2"></td>'; } 
						?>
					</tr>
					<tr>
						<?php 
							$formJeu = array();
							$nomFJ   = array();
							$cptForm = 0;
							$reponse = $bdd->query('SELECT id_formjeu, nom FROM ref_formulejeu;');
							while ($lstFormJeu = $reponse->fetch())
							{ 	
								$cptForm = $cptForm + 1 ;
								$formJeu[$cptForm] = $lstFormJeu['id_formjeu']; 
								$nomFJ[$cptForm]   = $lstFormJeu['nom']; 
							}
							$reponse->closeCursor();
						?>
						<td class="ps-color_sec">Règle</td>
						<?php
							for($i = 1; $i <= $nbTrou; $i++) 
							{
								echo '<td><select class="w3-select" name="regle'.$i.'" id="regle'.$i.'">';
								for ($j = 1; $j <= count($formJeu) ; $j++) 
								{		
									if ($fjTrou[$i] <> $formJeu[$j]) { echo '<option value="'.$formJeu[$j].'">'.$nomFJ[$j].'</option>'; }
									else { echo '<option value="'.$formJeu[$j].'" selected="selected">'.$nomFJ[$j].'</option>'; }
										
								}
								echo '</select>';								
							}
						?>
					</tr>
					
				</table>
				
				<br><br>
				<button onClick="stockeParcours();" type="submit" class="w3-button w3-block w3-padding-large ps-color w3-margin-bottom">Valider</button>
				
				<p id="resultat">--</p>

			</div>
		</div>
		
		<?php include("../../php/body_down.php");  ?>
		
			
		<script>
			//RecalculParcours
			function recalculParcours() 
			{
				// Déclaration des variables
				var l_idComp    = "";
				var l_nbTrou 	= "";
				
				// Calcul des variables 
				l_idComp = document.getElementById("idComp").value;
				l_nbTrou = document.getElementById("nbTrou").value;
				
				var obj, dbParam, xmlhttp, myObj, x, txt = "";
				obj = {"id_comp":l_idComp, "nbTrou":l_nbTrou};
				dbParam = JSON.stringify(obj);
				xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						location.reload();
					}
				};
				xmlhttp.open("POST", "//site/v0/json/recalculParcours.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send("x=" + dbParam);				
			}

			
		
			// Enregistre le parcours saisi
			function stockeParcours() {
				document.getElementById("resultat").innerHTML = "dde enreg";
				var l_idComp, l_nbTrou ;
				var l_parTrou = [];
				var l_frjTrou = [];
				
				l_idComp = document.getElementById("idComp").value;
				l_nbTrou = document.getElementById("nbTrou").value;
				// Récupération de l'id Comp et de parTrou
				for(let i = 1 ; i <= l_nbTrou ; i++)
				{
					l_trou = "trou" + i;
					l_regle = "regle" + i;
					l_parTrou[i - 1] = document.getElementById(l_trou).value;
					l_frjTrou[i - 1] = document.getElementById(l_regle).options[document.getElementById(l_regle).selectedIndex].value;
				}

				// Récupération des détails du trou dans la base sgc
				var obj, dbParam, xmlhttp, myObj, x, txt = "";
				obj = { "id_comp":l_idComp, "nbTrou":l_nbTrou, "parTrou":l_parTrou, "frjTrou":l_frjTrou};
				dbParam = JSON.stringify(obj);
				xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						document.getElementById("resultat").innerHTML = "OK"
						// enregistrement mis à jour
					}
				};
				xmlhttp.open("POST", "//site/v0/json/enregParcoursV0.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send("x=" + dbParam);				
			}
		</script>
	</body>
</html>
