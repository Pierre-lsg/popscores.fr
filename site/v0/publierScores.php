<?php include("../../php/before.php");  ?>
<?php include("commun/before.php");  ?>

<!DOCTYPE html>
<html>
	<title>Publier scores</title>
	<?php include("../../php/header.php");  ?>
	
	<body> 
		<?php include("../../php/connectdb.php");  ?>
		<?php include("commun/menuEtape.php");  ?>

		<!-- !PAGE CONTENT! -->
		<div class="w3-main" style="margin-left:340px;margin-right:40px">

			<div style="text-align:center;margin-top:80px">
				<?php 
					$reponse = $bdd->query('SELECT c.nom, c.saison, c.saison, e.nom as nom_comp, e.etape, e.id_comp, DATE_FORMAT(e.dateC,"%d/%m") AS dateC FROM championnat c, competition e WHERE e.id_comp = ' . $_GET["id_comp"] . ' AND c.id_champ = e.id_champ');
					while ($donnees = $reponse->fetch())
					{ echo '<h1 class="w3-xxxlarge ps-text-color">' . $donnees['nom'] . " " . $donnees['saison'] . " <br> #" . $donnees['etape'] . " " . $donnees['nom_comp'] . " - " . $donnees['dateC'] . '</h1>'; }
					$reponse->closeCursor();
				?>

				<!-- Champs cachés -->
				<!-- Identifiant de compétition -->
				<?php 
				if (isset($_SESSION['id_champ'])) {$l_idChamp = $_SESSION['id_champ']; }
				else {$l_idChamp = '5';}
				?>				

				<input style="visibility:hidden" id="idChamp" value="<?php echo $l_idChamp; ?>">
				<input style="visibility:hidden" id="idComp" value="<?php echo $_GET["id_comp"]; ?>">


				<!-- Afficher la liste -->
				<br><br>
				<button onClick="calculClassement();" type="submit" class="w3-button w3-block w3-padding-large ps-color w3-margin-bottom">Calculer le classement</button>

				<!-- Tableaux de classement  de la compétition -->
				<h2 class="w3-xxlarge ps-text-color"><b>Résultats de la compétition :</b> <a href='export/csv_resultatCompetition.php?id_comp=<?php echo $_GET["id_comp"]; ?>'><img src="/~popscores/img/expcsv.jpg" alt="Export CSV"style="width:32px;height:32px;"></a></h2>
				<h2 class="w3-xxlarge ps-text-color"><b>Classement de la compétition</b></h2>

				<table class="w3-table">
					<tr>
						<td>
							<table class="w3-table" id="classementJoueur">
								<tr>
									<th><a href='export/csv_classementJoueur.php?id_comp=<?php echo $_GET["id_comp"]; ?>'><img src="/~popscores/img/expcsv.jpg" alt="Export CSV"style="width:32px;height:32px;"></a></th>
									<th></th>
									<th></th>
								</tr>
								<tr class="ps-color_sec" >
									<th>Classement </th>
									<th>Joueur</th>
									<th>Points</th>
									<th>Score</th>
								</tr>
								<?php
									$numligne     = 0;
									$classement   = '';
									$nbScorePrec = 0;
									$classeLigne  = '';
								
									$sql='SELECT CONCAT(j.prenom, " " ,j.nom) AS joueur, c.score, c.resultat FROM classement_comp c, joueur j WHERE c.id_comp = ' . $_GET["id_comp"] . ' AND c.id_catClass = 1 AND j.id_joueur = c.id_cat ORDER BY(c.score) DESC, c.resultat, joueur';
									$req = $bdd->query($sql);
									if ($req->rowCount() <> 0)
									{
										while ($donnees = $req->fetch())
										{ 
											$numligne = $numligne + 1 ;
									
											if ($nbScorePrec != $donnees['resultat']) { $classement = $numligne ;}
											else { $classement = ''; }

											if ($numligne % 2 == 0) { $classeLigne = 'w3-light-gray'; }
											else { $classeLigne = ''; } 
											
											echo '<tr class="' . $classeLigne . '"><td>' . $classement . '</td><td>' . $donnees['joueur'] . '</td><td>' . $donnees['score'] . '</td><td>' . $donnees['resultat'] . '</td></tr>';
											
											$nbScorePrec = $donnees['resultat'];
										}
									}
								
								?>
							</table>
						</td>
						<td>
							<table class="w3-table" id="classementEquipe">
								<tr>
									<th><a href='export/csv_classementEquipe.php?id_comp=<?php echo $_GET["id_comp"]; ?>'><img src="/~popscores/img/expcsv.jpg" alt="Export CSV"style="width:32px;height:32px;"></a></th>
									<th></th>
									<th></th>
								</tr>
								<tr class="ps-color_sec" >
									<th>Classement</th>
									<th>Equipe</th>
									<th>Points</th>
									<th>Score</th>
								</tr>
								<?php
									$numligne     = 0;
									$classement   = '';
									$nbScorePrec = 0;
									$classeLigne  = '';
								
									$sql='SELECT e.nom, c.score, c.resultat, cl.logo FROM classement_comp c, equipe e, club cl WHERE c.id_comp = ' . $_GET["id_comp"] . ' AND c.id_catClass = 2 AND e.id_equipe = c.id_cat AND cl.id_club = e.id_club ORDER BY(c.score) DESC, nom';
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
											
											echo '<tr class="' . $classeLigne . '"><td>' . $classement . '  - <img src="/~popscores/img/clubs/' . $donnees['logo'] . '"></td><td>' . $donnees['nom'] . '</td><td>' . $donnees['score'] . '</td><td>' . $donnees['resultat'] . '</td></tr>';
											
											$nbScorePrec = $donnees['score'];
										}
									}
								
								?>
							</table>
						</td>
					</tr>
				</table>

				<!-- Tableaux de classement  du championnat -->
				<h2 class="w3-xxlarge ps-text-color"><b>Classement du championnat</b></h2>

				<table class="w3-table">
					<tr>
						<td>
							<table class="w3-table" id="classementGeneralJoueur">
								<tr>
									<th><a href='export/csv_classementGeneralJoueur.php?id_champ=<?php echo $l_idChamp; ?>'><img src="/~popscores/img/expcsv.jpg" alt="Export CSV"style="width:32px;height:32px;"></a></th>
									<th></th>
									<th></th>
								</tr>
								<tr class="ps-color_sec" >
									<th>Classement </th>
									<th>Joueur</th>
									<th>Points</th>
								</tr>
								<?php
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
								
								?>
							</table>
						</td>
						<td>
							<table class="w3-table" id="classementGeneralClub">
								<tr>
									<th><a href='export/csv_classementGeneralClub.php?id_champ=<?php echo $l_idChamp; ?>'><img src="/~popscores/img/expcsv.jpg" alt="Export CSV"style="width:32px;height:32px;"></a></th>
									<th></th>
									<th></th>
								</tr>
								<tr class="ps-color_sec" >
									<th>Classement</th>
									<th>Club</th>
									<th>Points</th>
								</tr>
								<?php
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
											
											echo '<tr class="' . $classeLigne . '"><td>' . $classement . '  - <img src="/~popscores/img/clubs/' . $donnees['logo'] . '"></td><td>' . $donnees['nom'] . '</td><td>' . $donnees['score'] . '</td></tr>';
											
											$nbScorePrec = $donnees['score'];
										}
									}
								
								?>
							</table>
						</td>
					</tr>
				</table>

			</div>
		</div>
		
		<?php include("../../php/body_down.php");  ?>
		
			
		<script>
			function calculClassement()
			{
				// Calcul classement compétition
				calculClassementX("Joueur");
				calculClassementX("Equipe");

				// Calcul classement championnat
				calculClassementX("GeneralJoueur");
				calculClassementX("GeneralClub");
			}
		
			function calculClassementX(typeClassement) 
			{
				var l_idComp = ''; 
				var l_idChamp = ''; 
								
				// Récupération de l'id compétition
				l_idComp  = document.getElementById("idComp").value;
				l_idChamp = document.getElementById("idChamp").value;

				// Appel à l'AS de calcul 
				var obj, dbParam, xmlhttp, myObj, x, txt = "";
				obj = { "idComp":l_idComp, "idChamp":l_idChamp};
				dbParam = JSON.stringify(obj);
				xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						myObj = JSON.parse(this.responseText);
						
						afficheClassement(myObj, typeClassement);
						
					}
				};
				xmlhttp.open("POST", "/~popscores/site/v0/json/calculeClassement"+ typeClassement +".php", false);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send("x=" + dbParam);			
			}
				
			function afficheClassement (obj, i_typeClass)
			{
				// Classement 
				var cell, ligne, numligne, l_classement, l_nbPointsPrec;
				var tableau = document.getElementById("classement" + i_typeClass);

				numligne = 0;
				l_nbPointsPrec = 0;
				l_classement = 0;

				// Suppression de la table précédente
				while (tableau.rows.length != 2)
				{	tableau.deleteRow(-1); }

				// Création de la table
				for (x in obj) {
					
					l_classement = l_classement + 1;
					numligne = numligne + 1 ;
					// nombre de lignes dans la table (avant ajout de la ligne)
					var nbLignes = tableau.rows.length;

					ligne = tableau.insertRow(-1); // création d'une ligne pour ajout en fin de table
					if (numligne % 2 == 0)
					{ ligne.setAttribute("class","w3-light-gray"); }

					// création et insertion des cellules dans la nouvelle ligne créée
					if (l_nbPointsPrec != obj[x].points)
					{
						cell = ligne.insertCell(0);
						cell.innerHTML = l_classement;
						if (i_typeClass == "Equipe" || i_typeClass == "GeneralClub")
						{	cell.innerHTML = cell.innerHTML + "  - <img src='/~popscores/img/clubs/" + obj[x].logo + "'>"; }
					}
					else 
					{
						cell = ligne.insertCell(0);
						if (i_typeClass == "Equipe" || i_typeClass == "GeneralClub")
						{	cell.innerHTML = "<img src='/~popscores/img/clubs/" + obj[x].logo + "'>"; }
					}
					
					cell = ligne.insertCell(1);
					cell.innerHTML = obj[x].joueur;

					cell = ligne.insertCell(2);
					cell.innerHTML = obj[x].points;
					l_nbPointsPrec = obj[x].points;
				}
			}
			

		</script>
	</body>
</html>
