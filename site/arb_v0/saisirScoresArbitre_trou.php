<?php include("../../php/beforeArbitre.php");  ?>
<?php include("commun/before.php");  ?>

<!DOCTYPE html>
<html>
	<title>Saisir scores</title>
	<?php include("../../php/header.php");  ?>
	<style>
		td {border-style:solid;}
		.sansBord {border-style:none;}
	</style>
	
	<body> 
		<!-- Mise à jour du score -->
		<div id="menu_joueur" style="position:fixed!important;right:10px;top:70px;">
			<b>
			<button type="button" class="w3-button w3-block w3-padding-large ps-color_sec w3-margin-bottom" onClick="ajoutePoint()">+</button>
			<button type="button" class="w3-button w3-block w3-padding-large ps-color_sec w3-margin-bottom" onClick="retirePoint()">-</button>
			</b>
		</div>
				
		<?php include("../../php/connectdb.php");  ?>
		<?php include("commun/menu.php");  ?>

		<!-- !PAGE CONTENT! -->
		<div class="w3-main" style="margin-left:340px;margin-right:40px">

			<div style="text-align:center;margin-top:80px">
				
				<?php 
					$reponse = $bdd->query('SELECT c.nom, c.saison, c.saison, e.nom as nom_comp, e.etape, e.nbJouParEqp, e.id_comp, DATE_FORMAT(e.dateC,"%d/%m") AS dateC FROM championnat c, competition e WHERE e.id_comp = ' . $_SESSION["id_comp"] . ' AND c.id_champ = e.id_champ');
					while ($donnees = $reponse->fetch())
					{ echo '<h1 class="w3-xxxlarge ps-text-color">' . $donnees['nom'] . " " . $donnees['saison'] . " <br> #" . $donnees['etape'] . " " . $donnees['nom_comp'] . " - " . $donnees['dateC'] . '</h1>';
					$l_nbJouParEqp = $donnees['nbJouParEqp'];
					}
					if (!isset($l_nbJouParEqp))
					{ $l_nbJouParEqp = '3'; }

					$reponse->closeCursor();
				?>

				<!-- Champs cachés -->
				<!-- Identifiant de compétition -->
				<input style="visibility:hidden" id="idComp" value="<?php echo $_SESSION["id_comp"]; ?>">
				<!-- Dernier champ score sélectionné -->
				<input style="visibility:hidden" id="idJoueur">

				<?php 
					// Saisie arbitre dès que la compétition a démarré (existence de Fly)
					// Le score n'est saisissable que lorsque la compétition a démarrée
					$l_debutComp = true;
					$reponse = $bdd->query('SELECT id_fly FROM flight WHERE id_comp = ' . $_SESSION["id_comp"]);
					if ($reponse->rowCount() == 0)
					{ 
						$l_debutComp = false;
						echo "<h1 class='w3-xxxlarge ps-alert-color'>La compétition n'a pas commencé !</h1>" ;
					}
					$reponse->closeCursor();
				?>
				
				<!-- Liste des trous de la compétition -->
				<select class="w3-select" name="trou" id="trou" onchange="changerTrou()">
					<?php
					if (isset($_SESSION["numTrou"]))
					{ $l_numTrou = $_SESSION["numTrou"];}
					else { $l_numTrou = 1; }
					if (isset($_GET["numTrou"])) { $l_numTrou = $_GET["numTrou"];}

					$reponse = $bdd->query('SELECT numero FROM `trou` WHERE id_comp = ' . $_SESSION["id_comp"] . ' ORDER BY numero;');
					while ($lstTrou = $reponse->fetch())
					{ 
						if ($lstTrou['numero'] == $l_numTrou)
						{
							echo '<option value="' . $lstTrou['numero'] . '" selected="selected"> Trou #' . $lstTrou['numero'] . '</option>' ;
						}
						else
						{
							echo '<option value="' . $lstTrou['numero'] . '"> Trou #' . $lstTrou['numero'] . '</option>' ;
						}
					}
					$reponse->closeCursor();
					?>
				</select>
				
				<br><br>

				<!-- Liste des flys de la compétition -->
				<select class="w3-select" name="fly" id="fly" onchange="changerFly()">
					<?php
					if (isset($_GET["numFly"]))
					{ $l_numFly = $_GET["numFly"];	}
					else { $l_numFly = 1; }
					$reponse = $bdd->query('SELECT DISTINCT(numero) FROM `flight` WHERE id_comp = ' . $_SESSION["id_comp"] . ' ORDER BY numero;');
					while ($lstFly = $reponse->fetch())
					{ 
						if ($lstFly['numero'] == $l_numFly)
						{
							echo '<option value="' . $lstFly['numero'] . '" selected="selected"> Fly #' . $lstFly['numero'] . '</option>' ;
						}
						else
						{
							echo '<option value="' . $lstFly['numero'] . '"> Fly #' . $lstFly['numero'] . '</option>' ;
						}
					}
					$reponse->closeCursor();
					?>
				</select>

				<br>
				
				<!-- Définition du fly  -->
				<?php
					// Calcul de l'entête 
					$l_formjeu = '';
					$tdPar = '';
					$l_idTrou = '';
					$lstPar = $bdd->query('SELECT t.par, t.id_trou, t.id_formjeu, f.nom FROM `trou` t, `ref_formulejeu` f WHERE `id_comp` = ' . $_SESSION["id_comp"] . ' AND `numero` = ' . $l_numTrou . ' AND t.id_formjeu = f.id_formjeu ;');
					while ($detTrou = $lstPar->fetch())
					{ 
						$l_formjeu = $detTrou['nom'];
						$l_idformjeu = $detTrou['id_formjeu'];
						$l_par = $detTrou['par'];
						$tdPar .= '<td>' . $detTrou['par'] . '</td>';
						$l_idTrou = $detTrou['id_trou'];
					}
					$lstPar->closeCursor();
				?>
				
				<!-- Identifiant du trou -->
				<input style="visibility:hidden" id="idTrou" value="<?php echo $l_idTrou; ?>">
				
				<?php					
					// Calcul du min et du max 
					if ($l_idformjeu == 4) 
					{ 
						$l_scoreMin = -3;
						$l_scoreMax = 0;
					} 
					else
					{
						$l_scoreMin = 0;
						$l_scoreMax = $l_par + 5;
					
					}

					// Affichage du Fly
					$l_nbJoueur = 0;
					$l_numEquipe = 0;
					$reponse = $bdd->query('SELECT f.numero, e.id_equipe, e.nom AS eqp_nom, j.prenom, j.nom, j.id_joueur AS idJoueur FROM `flight` f, `equipe` e, `joueur` j WHERE f.id_comp = ' . $_SESSION["id_comp"] . ' AND f.numero = ' . $l_numFly . ' AND f.id_equipe = e.id_equipe AND f.id_joueur = j.id_joueur ORDER BY f.numero, f.id_equipe ;');
					while ($listeFly = $reponse->fetch())
					{ 
						if ($l_nbJoueur == 0)
						{
							$l_nbJoueur++;
							echo '<div><table class="w3-table">
								<tr>
									<td width="20%" class="sansBord"></td><td width="20%" class="sansBord"></td><td width="20%" class="sansBord">Trou</td><td>' . $l_numTrou . '</td>
								</tr>
								<tr>
									<td width="20%" class="sansBord"></td><td width="20%" class="sansBord"></td><td width="20%" class="sansBord">Formule</td><td>' . $l_formjeu . '</td>
								</tr>
								<tr>
									<td class="sansBord"></td><td class="sansBord"></td><td class="sansBord">Par</td>' . $tdPar . '</tr>';	
						}
						
						// Liste des joueurs de l'équipe
						// Si nouvelle équipe
						if ($l_numEquipe <> $listeFly['id_equipe'])
						{	echo '<tr class="joueur" idJoueur="'.$listeFly['idJoueur'].'"><td rowspan="'.$l_nbJouParEqp.'">' . $listeFly['eqp_nom'] . '</td>'; 
							$l_numEquipe = $listeFly['id_equipe'];
						}
						else
						{	echo '<tr class="joueur" idJoueur="'.$listeFly['idJoueur'].'">'; }
						
						echo '<td class="ps-prenom">' . $listeFly['prenom'] . '</td><td class="ps-nom">' . $listeFly['nom'] . '</td>';
						$l_numTrou = 1;
						
						$scoreJoueur = $bdd->query('SELECT score FROM `resultat` WHERE id_comp = ' . $_SESSION["id_comp"] . ' AND id_joueur = ' . $listeFly['idJoueur'] . ' AND id_trou = ' . $l_idTrou . ' ORDER BY id_trou ;');
						if ($scoreJoueur->rowCount() <> 0)
						{
							$listeScore = $scoreJoueur->fetch();
							echo '<td id="scoreTrou' . $l_numTrou . '"><input size="2" id="scoreJoueur' . $listeFly['idJoueur'] . '" type="number" min="' . $l_scoreMin . '" max="' . $l_scoreMax . '" value="' . $listeScore['score'] . '"  onfocus="selectScoreJoueur(this)" /></td>';
						}							
						else
						{
							echo '<td width="50px" id="scoreTrou' . $l_numTrou . '"><input size="2" id="scoreJoueur' . $listeFly['idJoueur'] . '" type="number" min="' . $l_scoreMin . '" max="' . $l_scoreMax . '" value="0" onfocus="selectScoreJoueur(this)" /></td>';
						}
						echo '</tr>';
						
					}
					echo '</table></div>';
					$reponse->closeCursor();
				?>
				
				<br><br>
				
				<button onClick="validerScore();" type="submit" class="w3-button w3-block w3-padding-large ps-color w3-margin-bottom" <?php if ($l_debutComp == false) {echo 'disabled';} ?> >Valider le Score</button>
				
				<p id="resultat">--</p>

			</div>
		</div>
		
		<?php include("../../php/body_down.php");  ?>
		
			
		<script>
			// Ajoute un point au dernier champ sélectionné
			function ajoutePoint()
			{
				var l_scoreJoueur = document.getElementById("idJoueur").value;
				var l_score = document.getElementById(l_scoreJoueur).value;
				
				var l_scoreMax = <?php echo $l_scoreMax ?> ;
				
				if (parseInt(l_score) < l_scoreMax )
				{
					document.getElementById(l_scoreJoueur).value = parseInt(l_score) + 1;
				}
			}
			
			// Enlève un point au dernier champ sélectionné
			function retirePoint()
			{
				var l_scoreJoueur = document.getElementById("idJoueur").value;
				var l_score = document.getElementById(l_scoreJoueur).value;
				
				var l_scoreMin = <?php echo $l_scoreMin ?> ;
				
				if (parseInt(l_score) > l_scoreMin )
				{
					document.getElementById(l_scoreJoueur).value = parseInt(l_score) - 1;
				}
			}
			
			// Sélectionne le champ score
			function selectScoreJoueur(obj)
			{
				document.getElementById("idJoueur").value = obj.id;
			}
			
			// Enregistre le score du joueur
			function enregistreScoreJoueur()
			{
				alert('enregistre score');
			}
			
		
			// Valide le score d'un joueur
			function validerJoueur(i_idJoueur, i_score)
			{
				// Déclaration des variables
				var l_idComp    = "";
				var l_idFly = document.getElementById("fly").value;
				
				// Calcul des variables 
				l_idComp = document.getElementById("idComp").value;
				l_idTrou = document.getElementById("idTrou").value;
							
				// Récupération des détails du trou dans la base sgc
				var obj, dbParam, xmlhttp, myObj, x, txt = "";
				obj = { "idComp":l_idComp,"joueur":i_idJoueur,"trou":l_idTrou,"score":i_score };
				dbParam = JSON.stringify(obj);
				xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						document.getElementById("resultat").innerHTML = "Score Fly #" + l_idFly + " enregistré"
						// location.reload();
					}
				};
				xmlhttp.open("POST", "/~popscores/site/arb_v0/json/valideScoreJoueurTrou.php", false);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send("x=" + dbParam);
			}
			
			function validerScore()
			{
				var l_listeScoreJoueur = document.getElementsByClassName("joueur");
				var l_scoreJoueur;
				var l_ligneScore;
				var resultat = "";
				var l_numTrou;
					
				for (var i in l_listeScoreJoueur)
				{
					l_scoreJoueur = l_listeScoreJoueur[i];
					if (l_scoreJoueur.nodeType === 1)
					{
						for (var j = 0 ; j < l_scoreJoueur.childNodes.length; j++)
						{	
							l_ligneScore = l_scoreJoueur.childNodes[j];
							if (l_ligneScore.nodeType === 1)
							{
								if (l_ligneScore.id.slice(0,9) == "scoreTrou")
								{
								resultat = resultat + l_ligneScore.childNodes[0].value + ",";
								}
							}
						}
						
						resultat = resultat.slice(0,resultat.length -1);
						validerJoueur(l_scoreJoueur.getAttribute("idJoueur"), resultat);
						resultat = "";
					}
				}
			}
			
			function changerTrou()
			{
				var l_idTrou = document.getElementById("trou").value;
				var l_idFly = document.getElementById("fly").value;
				var link = "/~popscores/site/arb_v0/saisirScoresArbitre_trou.php?id_comp="+document.getElementById("idComp").value+"&numTrou="+l_idTrou+"&numFly="+l_idFly;
				
				
				open(link,"_self");
			}

			function changerFly()
			{
				var l_idTrou = document.getElementById("trou").value;
				var l_idFly = document.getElementById("fly").value;
				var link = "/~popscores/site/arb_v0/saisirScoresArbitre_trou.php?id_comp="+document.getElementById("idComp").value+"&numTrou="+l_idTrou+"&numFly="+l_idFly;
				
				
				open(link,"_self");
			}

		</script>
	</body>
</html>
