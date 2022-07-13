<?php include("../../php/before.php");  ?>
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
		<?php include("../../php/connectdb.php");  ?>
		<?php include("commun/menuEtape.php");  ?>

		<!-- !PAGE CONTENT! -->
		<div class="w3-main" style="margin-left:340px;margin-right:40px">

			<div style="text-align:center;margin-top:80px">
				<?php 
					$reponse = $bdd->query('SELECT c.nom, c.saison, c.saison, e.nom as nom_comp, e.etape, e.nbJouParEqp, e.id_comp, DATE_FORMAT(e.dateC,"%d/%m") AS dateC FROM championnat c, competition e WHERE e.id_comp = ' . $_GET["id_comp"] . ' AND c.id_champ = e.id_champ');
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
				<input style="visibility:hidden" id="idComp" value="<?php echo $_GET["id_comp"]; ?>">

				<!-- Liste des flys de la compétition -->
				<select class="w3-select" name="fly" id="fly" onchange="changerFly()">
					<?php
					if (isset($_GET["numFly"]))
					{ $l_numFly = $_GET["numFly"];	}
					else { $l_numFly = 1; }
					$reponse = $bdd->query('SELECT DISTINCT(numero) FROM `flight` WHERE id_comp = ' . $_GET["id_comp"] . ' ORDER BY numero;');
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
				
				<br><br>

				
				<!-- Définition du fly  -->
				<?php
					// Calcul de l'entête 'Par' et 'Formule de jeu'
					$tdPar = '';
					$tdFormule = '';
					$lstPar = $bdd->query('SELECT t.par, f.nomabr FROM trou t, ref_formulejeu f WHERE t.id_comp = ' . $_GET["id_comp"] . ' AND t.id_formjeu = f.id_formjeu ORDER BY t.numero;');
					while ($parTrou = $lstPar->fetch())
					{ $tdPar     .= '<td>' . $parTrou['par'] . '</td>';
					  $tdFormule .= '<td>' . $parTrou['nomabr'] . '</td>';}
					$lstPar->closeCursor();

					// Affichage du Fly
					$l_nbJoueur = 0;
					$l_numEquipe = 0;
					$reponse = $bdd->query('SELECT f.numero, e.id_equipe, e.nom AS eqp_nom, c.logo, j.prenom, j.nom, j.id_joueur AS idJoueur FROM `flight` f, `equipe` e, `club` c, `joueur` j WHERE f.id_comp = ' . $_GET["id_comp"] . ' AND f.numero = ' . $l_numFly . ' AND f.id_equipe = e.id_equipe AND c.id_club = e.id_club AND f.id_joueur = j.id_joueur ORDER BY f.numero, f.id_equipe ;');
					while ($listeFly = $reponse->fetch())
					{ 
						if ($l_nbJoueur == 0)
						{
							$l_nbJoueur++;
							echo '<div><table class="w3-table">
								<tr>
									<td width="17%" class="sansBord"><h3>Fly ' . $listeFly['numero'] . '</h3></td><td width="17%" class="sansBord"></td><td width="17%" class="sansBord">Trou</td>' ;
										
							for ($idTrou = 1 ; $idTrou <= $nbTrou ; $idTrou++)
							{ echo '<td>'.$idTrou.'</td>'; }
						
							echo '</tr>
								<tr><td class="sansBord"></td><td class="sansBord"></td><td class="sansBord">Par</td>' . $tdPar . '</tr>	
								<tr><td class="sansBord"></td><td class="sansBord"></td><td class="sansBord">Formule</td>' . $tdFormule . '</tr>';	
						}
						
						// Liste des joueurs de l'équipe
						// Si nouvelle équipe
						if ($l_numEquipe <> $listeFly['id_equipe'])
						{	echo '<tr class="joueur" idJoueur="'.$listeFly['idJoueur'].'"><td rowspan="'.$l_nbJouParEqp.'">' . $listeFly['eqp_nom'] . '<br><img src="/~popscores/img/clubs/' . $listeFly['logo'] . '"></td>'; 
							$l_numEquipe = $listeFly['id_equipe'];
						}
						else
						{	echo '<tr class="joueur" idJoueur="'.$listeFly['idJoueur'].'">'; }
						
						echo '<td class="ps-prenom">' . $listeFly['prenom'] . '</td><td class="ps-nom">' . $listeFly['nom'] . '</td>';
						$l_numTrou = 1;
						
						$trous = $bdd->query('SELECT id_trou FROM `trou` WHERE id_comp = ' . $_GET["id_comp"] . ' ORDER BY id_trou ;');
						while ($listeTrou = $trous->fetch())
						{							
							$trouAffiche = false;

							$scoreJoueur = $bdd->query('SELECT score, id_trou FROM `resultat` WHERE id_comp = ' . $_GET["id_comp"] . ' AND id_joueur = ' . $listeFly['idJoueur'] .  ' AND id_trou = ' . $listeTrou['id_trou'] . ' ORDER BY id_trou ;');
							while ($listeScore = $scoreJoueur->fetch())
							{
								echo '<td id="scoreTrou' . $l_numTrou . '"><input size="2" type="number" min="-3" max="20" value="' . $listeScore['score'] . '"/></td>';
								$trouAffiche = true;
							}
							
							if (!$trouAffiche)
							{
								echo '<td width="50px" id="scoreTrou' . $l_numTrou . '"><input size="2" type="number" min="-3" max="20" value=""/></td>';
							}
							$l_numTrou++;							
						}
						echo '</tr>';
						
					}
					echo '</table></div>';
					$reponse->closeCursor();
				?>
				
				<br><br>
				
				<button onClick="validerFly();" type="submit" class="w3-button w3-block w3-padding-large ps-color w3-margin-bottom">Valider le Fly</button>
				
				<p id="resultat">--</p>

			</div>
		</div>
		
		<?php include("../../php/body_down.php");  ?>
		
			
		<script>
			// Valide le score d'un joueur
			function validerJoueur(i_idJoueur, i_score)
			{
				// Déclaration des variables
				var l_idComp    = "";
				
				// Calcul des variables 
				l_idComp = document.getElementById("idComp").value;
			
				// Récupération des détails du trou dans la base sgc
				var obj, dbParam, xmlhttp, myObj, x, txt = "";
				obj = { "idComp":l_idComp,"joueur":i_idJoueur,"score":i_score };
				dbParam = JSON.stringify(obj);
				xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						// location.reload();
					}
				};
				xmlhttp.open("POST", "/~popscores/site/v0/json/valideScoreJoueur.php", false);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send("x=" + dbParam);
			}
			
			function validerFly()
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
						// alert(l_scoreJoueur.childNodes[1].innerHTML);
						// alert(l_scoreJoueur.childNodes.length);
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
						
						//
						// alert(l_scoreJoueur.getAttribute("idJoueur"));
						// alert(l_scoreJoueur.childNodes[3].value);
						resultat = resultat.slice(0,resultat.length -1);
						validerJoueur(l_scoreJoueur.getAttribute("idJoueur"), resultat);
						resultat = "";
					}
				}
			}

			function changerFly()
			{
				var l_idFly = document.getElementById("fly").value;
				var link = "/~popscores/site/v0/saisirScores_fly.php?id_comp="+document.getElementById("idComp").value+"&numFly="+l_idFly;
				
				
				open(link,"_self");
			}

		</script>
	</body>
</html>
