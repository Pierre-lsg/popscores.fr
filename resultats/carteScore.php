<!DOCTYPE html>
<html>
	<title>Carte de scores</title>
	<?php include("../php/header.php");  ?>
	
	<body> 
		<?php include("../php/connectdb.php");  ?>

		<!-- !PAGE CONTENT! -->
		<div class="w3-main" style="margin-left:40px;margin-right:40px">

			<div style="text-align:center;margin-top:80px">
				<!-- Identifiant de compétition -->
				<?php 
					if (isset($_GET['id_comp'])) 
					{ 
						if (is_numeric($_GET['id_comp'])) { $l_idComp = $_GET['id_comp']; }
						else { $l_idComp = 0;	}
					}
					else { $l_idComp = 0;	}
				?>				

				<!-- Identifiant et informations du joueur -->
				<?php
					if (isset($_GET['id_joueur'])) 
					{ 
						if (is_numeric($_GET['id_joueur'])) { $l_idJoueur = $_GET['id_joueur']; }
						else { $l_idJoueur = 0;	}
					}
					else { $l_idJoueur = 0;	}				
								
					if ($l_idJoueur <> 0)
					{
						$reponse = $bdd->query('SELECT concat(prenom, " ", nom) AS nomJoueur FROM `joueur` WHERE id_joueur = ' . $l_idJoueur);
						while ($donnees = $reponse->fetch())
						{ 
							$l_nomJoueur = $donnees['nomJoueur'];
						}
						$reponse->closeCursor();
					}
					else { $l_nomJoueur = "Gérard BOUCHARD"; }
				?>
				
				<!-- Détails de la compétition -->
				<?php 
					$l_estChampionnatInterne = 0;
					$reponse = $bdd->query('SELECT c.interne, c.nom, c.saison, c.saison, e.nom as nom_comp, e.etape, e.id_comp, DATE_FORMAT(e.dateC,"%d/%m") AS dateC FROM championnat c, competition e WHERE e.id_comp = ' . $l_idComp . ' AND c.id_champ = e.id_champ');
					while ($donnees = $reponse->fetch())
					{ 
						$l_titre = $donnees['nom'] . " " . $donnees['saison'] . " #" . $donnees['etape'] . " " . $donnees['nom_comp'] . " - " . $donnees['dateC'];
						echo '<h2 class="w3-xxlarge ps-text-color">' . $l_titre . '</h2>'; 
						$l_estChampionnatInterne = $donnees['interne'];
					}
					$reponse->closeCursor();
				?>

				<!-- Champs cachés -->
				<input style="visibility:hidden" id="estChampInterne" value="<?php echo $l_estChampionnatInterne; ?>">
				<input style="visibility:hidden" id="idComp" value="<?php echo $l_idComp; ?>">
				<input style="visibility:hidden" id="idJoueur" value="<?php echo $l_idJoueur; ?>">


				<!-- Afficher la carte de score -->
				<h3 class="w3-xlarge ps-text-color"><b>Carte de score de <?php echo $l_nomJoueur; ?></b></h3>

				<table class="w3-table">
					<tr>
						<td>
							<table class="w3-table" id="Carte de scores">
								<tr class="ps-color_sec" >
									<th>Trou</th>
									<th>Par</th>
									<th>Nb Coups</th>
									<th>Delta</th>
								</tr>
								<?php
									$numligne     = 0;
									$l_totalPar   = 0;
									$l_totalScore = 0;
									$l_totalDelta = 0;									
									$l_carteScore = '';
									$sql='SELECT t.numero, t.par, r.score, (r.score - t.par) AS delta FROM resultat r, trou t WHERE r.id_comp = ' . $l_idComp . ' AND r.id_joueur = ' . $l_idJoueur . ' AND r.score <> 0 AND r.id_trou = t.id_trou ORDER BY t.numero ASC';
									$req = $bdd->query($sql);
									if ($req->rowCount() <> 0)
									{
										while ($donnees = $req->fetch())
										{ 
											$numligne = $numligne + 1 ;
	
											if ($numligne % 2 == 0) { $classeLigne = 'w3-light-gray'; }
											else { $classeLigne = ''; } 
											
											$l_totalPar   = $l_totalPar   + intval($donnees['par']);
											$l_totalScore = $l_totalScore + intval($donnees['score']);
											$l_totalDelta = $l_totalDelta + intval($donnees['delta']);
											
											$l_carteScore = $l_carteScore . '<tr class=' . $classeLigne . '><td>' . $donnees['numero'] . '</td><td>' . $donnees['par'] . '</td><td>' . $donnees['score'] . '</td><td>' . $donnees['delta'] . '</td></tr>';
										}
									}
									$l_carteScore = $l_carteScore . '<tr class="ps-color"><td>Total</td><td>' . $l_totalPar . '</td><td>' . $l_totalScore . '</td><td>' . $l_totalDelta . '</td></tr>';
									
									echo $l_carteScore;
								
								?>
							</table>
						</td>
					</tr>
				</table>

			</div>
		</div>

		<!-- Partage réseaux sociaux -->
		<div class='w3-center'>
			<a target="_blank" title="Facebook" href="https://www.facebook.com/sharer.php?u=<?php $l_titre; ?>&t=<?php "A completer"; ?>" rel="nofollow"  onclick="javascript:window.open(this.href, '', '');return false;"><img src="../img/mini/facebook.png" alt="Facebook" height="32" width="32"></a>
			&nbsp; &nbsp; 
			<img src="../img/mini/instagram.png" alt="Instagram" height="32" width="32"> 
			&nbsp; &nbsp; 
			<a target="_blank" title="Envoyer par mail" href="mailto:?subject=<?php echo $l_titre; ?>&body='<?php echo "A completer"; ?>" rel="nofollow"><img src="../img/mini/mail.png" alt="Mail" height="32" width="32"></a>
			&nbsp; &nbsp; 
			<img src="../img/mini/pinterest.png" alt="Pinterest" height="32" width="32"> 
			&nbsp; &nbsp; 
			 <a target="_blank" title="Twitter" href="https://twitter.com/share?url=vide&text=vide&via=popscores" rel="nofollow" onclick="javascript:window.open(this.href, '', '');return false;"><img src="../img/mini/twitter.png" alt="Twitter" height="32" width="32"></a>
		</div>
		
		<!-- LSG.CSS Container -->
		<div id='ps_footer' class="w3-light-grey w3-container w3-padding-32" style="margin-top:75px;padding-right:58px"><p class="w3-right">Powered by <a href="https://www.lyonstreetgolf.fr" title="LSG.CSS" target="_blank" class="w3-hover-opacity">Lyon Street Golf</a></p><p id='ps_ident'></p></div>
  

		<script>
		
		function test()
		{
			alert("1");
		}
		</script>
  
	</body>
</html>
