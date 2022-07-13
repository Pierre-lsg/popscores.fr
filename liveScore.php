<!DOCTYPE html>
<html>
	<title>Résultats FCPT</title>
	<?php include("php/header.php");  ?>
	
	<body> 
		<?php include("php/connectdb.php");  ?>

		<!-- !PAGE CONTENT! -->
		<div class="w3-main" style="margin-left:40px;margin-right:40px">

			<div style="text-align:center;margin-top:80px">
				<!-- Identifiant de compétition -->
				<?php 
					/* Championnat à afficher par défaut */
					$l_idChamp = '6';
					$reponse = $bdd->query('SELECT `val` FROM `param` WHERE `param` = "champ_result"');
					while ($lstParam = $reponse->fetch())
					{ 
						$l_idChamp = $lstParam['val'];
					}
					
					/* TODO : définir une constante pour sélectionner championnat et compétition par défaut */
					/* Compétition à afficher par défaut - la dernière du championnat*/
					$l_idComp = 13;
					if (isset($_GET['id_comp'])) { $l_idComp = $_GET['id_comp']; }
					else 
					{
						$reponse = $bdd->query('SELECT `id_comp`,`nom` FROM `competition` WHERE `id_champ` = ' . $l_idChamp . ' ORDER BY `id_comp` LIMIT 1;');
						while ($lstComp = $reponse->fetch())
						{ 
							$l_idComp = $lstComp['id_comp']; 
						}
						$reponse->closeCursor();
					}
				?>				

				<?php 
					$reponse = $bdd->query('SELECT c.nom, c.saison, c.saison, e.nom as nom_comp, e.etape, e.id_comp, DATE_FORMAT(e.dateC,"%d/%m") AS dateC FROM championnat c, competition e WHERE e.id_comp = ' . $l_idComp . ' AND c.id_champ = e.id_champ');
					while ($donnees = $reponse->fetch())
					{ echo '<h1 class="w3-xxxlarge ps-text-color">' . $donnees['nom'] . " " . $donnees['saison'] . " <br> #" . $donnees['etape'] . " " . $donnees['nom_comp'] . " - " . $donnees['dateC'] . '</h1>'; }
					$reponse->closeCursor();
				?>

				<!-- Champs cachés -->
				<input style="visibility:hidden" id="idChamp" value="<?php echo $l_idChamp; ?>">
				<input style="visibility:hidden" id="idComp" value="<?php echo $l_idComp; ?>">


				<!-- Afficher la liste -->
				<br><br>
				<form action="liveScore.php">
					<table class="w3-table">
						<tr>
							<td width="50px">Compétition</td>
							<td>
								<!-- Liste des équipes -->
								<select class="w3-select" name="id_comp" id="id_comp" required>
									<?php
									$reponse = $bdd->query('SELECT `id_comp`,`nom` FROM `competition` WHERE `id_champ` = ' . $l_idChamp . ' ;');
									while ($lstComp = $reponse->fetch())
									{ 
									if ($lstComp['id_comp'] <> $l_idComp)
										{ echo '<option value="' . $lstComp['id_comp'] . '">' . $lstComp['nom'] . '</option>' ; }
									else
										{ echo '<option value="' . $lstComp['id_comp'] . '" selected>' . $lstComp['nom'] . '</option>' ; }
									}
									$reponse->closeCursor();
									?>
								</select>
							</td>
						</tr>
					</table>

					<input type="submit" value="Sélectionner le classement" class="w3-button w3-block w3-padding-large ps-color w3-margin-bottom" method="post">
				</form> 

				<!-- Meilleur score par trou -->
				<h2 class="w3-xxlarge ps-text-color"><b>Top Score </b></h2>
				<?php 
					// Pour chaque Trou de la compétition
					$reponse = $bdd->query('SELECT `numero`,`id_trou` FROM `trou` WHERE `id_comp` = ' . $l_idComp . ' ORDER BY `numero`;');
					while ($lstTrou = $reponse->fetch())
					{ 			

						$l_meilleurScore = "";
						// Meilleur résultat
						$repScore = $bdd->query('SELECT MIN(score) AS bestScore FROM `resultat` WHERE `id_comp` = ' . $l_idComp . ' AND `id_trou` =  '. $lstTrou['id_trou'] .' ;');
						while ($lstScore = $repScore->fetch())
						{ 				
							$l_meilleurScore = $lstScore['bestScore'];
						}
						$repScore->closeCursor();
						
						// Afficher le meilleur score
						if ($l_meilleurScore <> "")
						{
							echo "<br><p> Trou #" . $lstTrou['numero'] . " : " . $l_meilleurScore . " coups</p>";
							
							// Afficher la liste des joueurs par trou
							$repJoueur = $bdd->query('SELECT j.prenom, j.nom FROM `joueur` j, `resultat` r WHERE j.id_joueur = r.id_joueur AND r.id_comp = ' . $l_idComp . ' AND r.id_trou = '. $lstTrou['id_trou'] .' AND r.score = ' . $l_meilleurScore . ';');
							
							while ($lstJoueur = $repJoueur->fetch())
							{ 				
									echo $lstJoueur['prenom'] . " " . $lstJoueur['nom'] ."<br>" ;
							}
							$repJoueur->closeCursor();
						}
						else
						{
							echo "<br><p> Trou #" . $lstTrou['numero'] . " : pas encore joué";
						}
					}
					$reponse->closeCursor();
				?>

			</div>
		</div>

		<!-- W3.CSS Container -->
		<div id='ps_footer' class="w3-light-grey w3-container w3-padding-32" style="margin-top:75px;padding-right:58px"><p class="w3-right">Powered by <a href="https://www.lyonstreetgolf.fr" title="W3.CSS" target="_blank" class="w3-hover-opacity">Lyon Street Golf</a></p><p id='ps_ident'></p></div>
  					
	</body>
</html>
