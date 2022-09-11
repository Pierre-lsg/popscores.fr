<!DOCTYPE html>
<html>
	<title>Résultats Championnat de France</title>
	<?php include("php/header.php");  ?>
	
	<body> 
		<?php include("php/connectdb.php");  ?>

		<!-- !PAGE CONTENT! -->
		<div class="w3-main" style="margin-left:40px;margin-right:40px">

			<div style="text-align:center;margin-top:80px">
				<!-- Identifiant de compétition -->
				<?php 
					/* Championnat à afficher par défaut */
					$l_idChamp = '13';
					if (isset($_GET['id_champ'])) { $l_idChamp = $_GET['id_champ']; }
					else 
					{
						$reponse = $bdd->query('SELECT `val` FROM `param` WHERE `param` = "champ_result"');
						while ($lstParam = $reponse->fetch())
						{ 
							$l_idChamp = $lstParam['val'];
						}
					}
					
					/* Compétition à afficher par défaut - la 1ère du championnat*/
					$l_idComp = 18;
					if (isset($_GET['id_comp'])) { $l_idComp = $_GET['id_comp']; }
					else 
					{
						$reponse = $bdd->query('SELECT `id_comp`,`nom` FROM `competition` WHERE `id_champ` = ' . $l_idChamp . ' ORDER BY `etape` LIMIT 1;');
						while ($lstComp = $reponse->fetch())
						{ 
							$l_idComp = $lstComp['id_comp']; 
						}
						$reponse->closeCursor();
					}
				?>				

				<?php
					// Le championnat est-il interne
					$l_estChampionnatInterne = 0;
					$reponse = $bdd->query('SELECT c.interne, c.nom, c.saison, c.saison, e.nom as nom_comp, e.etape, e.id_comp, DATE_FORMAT(e.dateC,"%d/%m") AS dateC, e.pourChampionnat FROM championnat c, competition e WHERE e.id_comp = ' . $l_idComp . ' AND c.id_champ = e.id_champ ORDER BY e.etape ');
					while ($donnees = $reponse->fetch())
					{ 
						echo '<h1 class="w3-xxxlarge ps-text-color">' . $donnees['nom'] . " " . $donnees['saison'] . " <br> #" . $donnees['etape'] . " " . $donnees['nom_comp'] . " - " . $donnees['dateC'] . '</h1>'; 
						if ($donnees['pourChampionnat'] == 0)
						{	echo '<h6>Ne compte pas pour le championnat</h6>'; }
						$l_estChampionnatInterne = $donnees['interne'];
					}
					$reponse->closeCursor();
					
					// Peut-on afficher les résultats de la compétition ? Date de non-affichage passée ?
					$l_estAffichable = 1;
					$reponse = $bdd->query('SELECT DATE_FORMAT(dateResultat,"%d/%m/%Y") AS dateRes FROM `competition` WHERE `id_comp` =  ' . $l_idComp . '  AND dateResultat > CURRENT_DATE; ');
					while ($donnees = $reponse->fetch())
					{ 
						$l_dateResultat = $donnees['dateRes'];
						$l_estAffichable = 0;
					}
					$reponse->closeCursor();
					
					// Véto sur les résultats du championnat ? 
					$l_estAffClassChamp = 1;
					$l_datecur = date_parse(date("Y-m-d"));
					$rDateC = $bdd->query('SELECT DATE_FORMAT(dateC,"%Y-%m-%d") AS dateComp, DATE_FORMAT(dateResultat,"%Y-%m-%d") AS dateRes FROM competition WHERE id_champ = ' . $l_idChamp . ' ;' );
					while ($donnees = $rDateC->fetch())
					{ 
						$l_dateRes = date_parse($donnees['dateRes']);
						$l_dateComp = date_parse($donnees['dateComp']);
						if ($l_datecur >= $l_dateComp and $l_datecur < $l_dateRes)
						{
							$l_estAffClassChamp = 0;
						}
					}
					$rDateC->closeCursor();
				?>

				<!-- Champs cachés -->
				<input style="visibility:hidden" id="estChampInterne" value="<?php echo $l_estChampionnatInterne; ?>">
				<input style="visibility:hidden" id="idChamp" value="<?php echo $l_idChamp; ?>">
				<input style="visibility:hidden" id="idComp" value="<?php echo $l_idComp; ?>">


				<!-- Afficher la liste -->
				<br><br>
				<form action="//resultats.php">
					<table class="w3-table">
						<tr>
							<td width="50px">Championnat</td>
							<td>
								<!-- Liste des championnats -->
								<select class="w3-select" name="id_champ" id="id_champ" onChange="changerChampionnat()" required>
									<?php
									$reponse = $bdd->query('SELECT `id_champ`,`nom`,`saison` FROM `championnat` WHERE `id_champ` NOT IN (1,2,3,4,6);');
									while ($lstChamp = $reponse->fetch())
									{ 
									if ($lstChamp['id_champ'] <> $l_idChamp)
										{ echo '<option value="' . $lstChamp['id_champ'] . '">' . $lstChamp['nom'] . ' - ' . $lstChamp['saison'] . '</option>' ; }
									else
										{ echo '<option value="' . $lstChamp['id_champ'] . '" selected>' . $lstChamp['nom'] . ' - ' . $lstChamp['saison'] . '</option>' ; }
									}
									$reponse->closeCursor();
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td width="50px">Compétition</td>
							<td>
								<!-- Liste des compétitions du championnat -->
								<select class="w3-select" name="id_comp" id="id_comp" onChange="changerClassement()" required>
									<?php
									$reponse = $bdd->query('SELECT `id_comp`,`nom` FROM `competition` WHERE `id_champ` = ' . $l_idChamp . ' ORDER BY `etape` ;');
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

					<!-- <input type="submit" value="Sélectionner le classement" class="w3-button w3-block w3-padding-large ps-color w3-margin-bottom" method="post"> -->
				</form> 

				<!-- Tableaux de classement  de la compétition -->
				<h2 class="w3-xxlarge ps-text-color"><b>Classement de la compétition</b></h2>

				<table class="w3-table">
					<?php
						if ($l_estAffichable <> 0)
						{
							echo '<tr><td class="w3-tiny" onClick="showHideTab(\'classementJoueur\');">afficher/masquer classement individuel</tr></td>';
							echo '<tr>
									<td>
										<table class="w3-table" id="classementJoueur">
											<tr class="ps-color_sec" >
												<th>Classement </th>
												<th>Joueur</th>
												<th>Nb Coups</th>
												<th>Points</th>
												<th>Championnat</th>
											</tr>';
							$numligne     = 0;
							$classement   = '';
							$nbScorePrec = 0;
							$classeLigne  = '';
						
							$sql='SELECT j.id_joueur, j.estCalculChampionnat, CONCAT(j.prenom, " " ,j.nom) AS joueur, c.score, c.scoreChamp, c.nbCoups FROM classement_comp c, joueur j WHERE c.id_comp = ' . $l_idComp . ' AND c.id_catClass = 1 AND j.id_joueur = c.id_cat ORDER BY(c.score) DESC, c.nbCoups, joueur';
							$req = $bdd->query($sql);
							if ($req->rowCount() <> 0)
							{
								while ($donnees = $req->fetch())
								{ 
									$numligne = $numligne + 1 ;
							
									if ($nbScorePrec != $donnees['nbCoups']) { $classement = $numligne ;}
									else { $classement = ''; }

									if ($donnees['estCalculChampionnat'] == 1) { $ptsJoueurChampionnat = $donnees['scoreChamp']; }
									else { $ptsJoueurChampionnat = ""; }
									
									if ($numligne % 2 == 0) { $classeLigne = 'w3-light-gray'; }
									else { $classeLigne = ''; } 
									
									echo '<tr class="' . $classeLigne . '"  onClick="showScoreCard(' . $donnees['id_joueur'] . ');"><td>' . $classement . '</td><td>' . $donnees['joueur'] . '</td><td>' . $donnees['nbCoups'] . '</td><td>' . $donnees['score'] . '</td><td><center>' . $ptsJoueurChampionnat . '</center></td></tr>';
									
									$nbScorePrec = $donnees['nbCoups'];
								}
							}
								
							echo '			</table>
										</td>
									</tr>';
						}	
						else
						{
							echo '<tr><td>Les résultats arrivent sur popscores le : ' . $l_dateResultat . ' </tr></td>';
						}
						if ($l_estAffichable <> 0)
						{
							echo '<tr><td class="w3-tiny" onClick="showHideTab(\'classementEquipe\');">afficher/masquer classement équipe</tr></td>';
						}
					?>
					
					<tr>
						<?php
							if ($l_estAffichable <> 0)
							{
								echo '<td>
									<table class="w3-table" id="classementEquipe">
										<tr class="ps-color_sec" >
											<th>Classement</th>
											<th>Equipe</th>
											<th>Score</th>
											<th>Points</th>
											<th>Championnat</th>
										</tr>' ;
								$numligne     = 0;
								$classement   = '';
								$nbScorePrec = 0;
								$classeLigne  = '';
							
								$sql='SELECT e.nom, e.id_equipe, e.estCalculChampionnat, c.score, c.scoreChamp, c.resultat, cl.logo FROM classement_comp c, equipe e, club cl WHERE c.id_comp = ' . $l_idComp . ' AND c.id_catClass = 2 AND e.id_equipe = c.id_cat AND cl.id_club = e.id_club ORDER BY(c.score) DESC, nom';
								$req = $bdd->query($sql);
								if ($req->rowCount() <> 0)
								{
									while ($donnees = $req->fetch())
									{ 
										$numligne = $numligne + 1 ;
								
										if ($nbScorePrec != $donnees['score']) { $classement = $numligne ;}
										else { $classement = ''; }

										if ($numligne % 2 == 0) { $classeLigne = 'w3-light-gray'; }
										else { $classeLigne = ''; } 

										if ($donnees['estCalculChampionnat'] == 1) { $ptsEquipeChampionnat = $donnees['scoreChamp']; }
										else { $ptsEquipeChampionnat = ""; }										
										
										echo '<tr class="' . $classeLigne . '"><td>' . $classement . '  - <img src="//img/clubs/' . $donnees['logo'] . '"></td><td>' . $donnees['nom'];
							
										$sql2='SELECT j.prenom FROM joueur j, joueur_comp jc, equipe e WHERE jc.id_comp = ' . $l_idComp . ' AND e.id_equipe = ' . $donnees['id_equipe'] . ' AND jc.id_joueur = j.id_joueur AND jc.id_equipe = e.id_equipe';
										$req2 = $bdd->query($sql2);
										if ($req2->rowCount() <> 0)
										{
											$l_listeJoueurs ='<br>(';
											while ($donnees2 = $req2->fetch())
											{
												$l_listeJoueurs = $l_listeJoueurs . $donnees2['prenom'] . ', ';
											}
											$l_listeJoueurs = substr($l_listeJoueurs, 0, -2) . ')';
											echo $l_listeJoueurs;

										}

										echo '</td><td>' . $donnees['resultat'] . '</td><td>' . $donnees['score'] . '</td><td><center>' . $ptsEquipeChampionnat . '</center></td></tr>';
										
										$nbScorePrec = $donnees['score'];
									}
								}
										
								echo '	</table>
								</td>';
							}
						?>
					</tr>
				</table>

				<?php
					if ($l_estAffClassChamp <> 0)
					{
						echo '<!-- Tableaux de classement  du championnat -->
						<h2 class="w3-xxlarge ps-text-color"><b>Classement du championnat</b></h2>

						<table class="w3-table">
							<tr><td class="w3-tiny" onClick="showHideTab(\'classementGeneralJoueur\');">afficher/masquer classement joueur</tr></td>
							<tr>
								<td>
									<table class="w3-table" id="classementGeneralJoueur">
										<tr class="ps-color_sec" >
											<th>Classement </th>
											<th>Joueur</th>
											<th>Points</th>
										</tr>';
										
											$numligne     = 0;
											$classement   = '';
											$nbScorePrec = 0;
											$classeLigne  = '';
										
											$sql='SELECT CONCAT(j.prenom, " " ,j.nom) AS joueur, c.score FROM classement_champ c, joueur j WHERE c.id_champ = ' . $l_idChamp . ' AND c.id_catClass = 1 AND j.id_joueur = c.id_cat ORDER BY(c.score) DESC, joueur';
											$req = $bdd->query($sql);
											if ($req->rowCount() <> 0)
											{
												while ($donnees = $req->fetch())
												{ 
													$numligne = $numligne + 1 ;
											
													if ($nbScorePrec != $donnees['score']) { $classement = $numligne ;}
													else { $classement = ''; }

													if ($numligne % 2 == 0) { $classeLigne = 'w3-light-gray'; }
													else { $classeLigne = ''; } 
													
													echo '<tr class="' . $classeLigne . '"><td>' . $classement . '</td><td>' . $donnees['joueur'] . '</td><td>' . $donnees['score'] . '</td></tr>';
													
													$nbScorePrec = $donnees['score'];
												}
											}
										
								
							echo '		</table>
								</td>
							</tr>';
							
								if ($l_estChampionnatInterne == 0 and $l_estAffClassChamp <> 0)
								{
									echo '<tr><td class="w3-tiny" onClick="showHideTab(\'classementGeneralClub\');">afficher/masquer classement équipe</tr></td>';
								}
							
							echo '<tr>';
								
									if ($l_estChampionnatInterne == 0 and $l_estAffClassChamp <> 0)
									{
										echo '<td>
											<table class="w3-table" id="classementGeneralClub">
												<tr class="ps-color_sec" >
													<th>Classement</th>
													<th>Club</th>
													<th>Points</th>
												</tr>';
										$numligne     = 0;
										$classement   = '';
										$nbScorePrec = 0;
										$classeLigne  = '';
									
										$sql='SELECT cl.nom, cl.logo, c.score FROM classement_champ c, club cl WHERE c.id_champ = ' . $l_idChamp . ' AND c.id_catClass = 3 AND cl.id_club = c.id_cat ORDER BY(c.score) DESC, nom';
										$req = $bdd->query($sql);
										if ($req->rowCount() <> 0)
										{
											while ($donnees = $req->fetch())
											{ 
												$numligne = $numligne + 1 ;
										
												if ($nbScorePrec != $donnees['score']) { $classement = $numligne ;}
												else { $classement = ''; }

												if ($numligne % 2 == 0) { $classeLigne = 'w3-light-gray'; }
												else { $classeLigne = ''; } 
												
												echo '<tr class="' . $classeLigne . '"><td>' . $classement . '  - <img src="//img/clubs/' . $donnees['logo'] . '"></td><td>' . $donnees['nom'] . '</td><td>' . $donnees['score'] . '</td></tr>';
												
												$nbScorePrec = $donnees['score'];
											}
										}
												
										echo	'</table>
										</td>';
									}
								
							echo '</tr>
						</table>';
					}
				?>

			</div>
		</div>

		<!-- W3.CSS Container -->
		<div id='ps_footer' class="w3-light-grey w3-container w3-padding-32" style="margin-top:75px;padding-right:58px"><p class="w3-right">Powered by <a href="https://www.lyonstreetgolf.fr" title="W3.CSS" target="_blank" class="w3-hover-opacity">Lyon Street Golf</a></p><p id='ps_ident'></p></div>
  

		<script>
		
		function showScoreCard(i_idJoueur)
		{
			l_idComp = document.getElementById("id_comp").options[document.getElementById("id_comp").selectedIndex].value;

			var link = "//resultats/carteScore.php?id_comp=" + l_idComp + "&id_joueur=" + i_idJoueur;
			
			open(link,"_self");
		}
		
		function showHideTab(classement)
		{
			var tbl = document.getElementById(classement);
			 
			if(tbl.style.display != 'none') 
			{ tbl.style.display = 'none';} 
			else 
			{ tbl.style.display = 'block'; }		
		}
			
		function changerClassement()
		{
			
			l_idChamp = document.getElementById("id_champ").options[document.getElementById("id_champ").selectedIndex].value;
			l_idComp = document.getElementById("id_comp").options[document.getElementById("id_comp").selectedIndex].value;

			var link = "//resultats.php?id_comp="+l_idComp+"&id_champ="+l_idChamp;
			
			open(link,"_self");
		}
		
		function changerChampionnat()
		{
			
			l_idChamp = document.getElementById("id_champ").options[document.getElementById("id_champ").selectedIndex].value;
			l_idComp = document.getElementById("id_comp").options[document.getElementById("id_comp").selectedIndex].value;

			var link = "//resultats.php?id_champ="+l_idChamp;
			
			open(link,"_self");
		}

		</script>
  
	</body>
</html>