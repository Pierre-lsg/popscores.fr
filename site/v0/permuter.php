<?php include("../../php/before.php");  ?>
<?php include("commun/before.php");  ?>

<!DOCTYPE html>
<html>
	<title>Liste Fly : Permuter équipe</title>
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
				
				
				<!-- Liste Fly et équipes -->
				<p>  Equipe 1 : </p>
				<!-- Liste des équipes -->
				<p>
					<select class="w3-select" name="equipe1" id="equipe1" required>
						<?php
						$reponse = $bdd->query('SELECT id_equipe, nom FROM equipe WHERE id_equipe IN (SELECT DISTINCT(id_equipe) FROM flight WHERE id_comp = ' . $_GET["id_comp"] . ') ORDER BY nom;');
						while ($lstEquipe = $reponse->fetch())
						{ echo '<option value="' . $lstEquipe['id_equipe'] . '">' . $lstEquipe['nom'] . '</option>' ;}
						$reponse->closeCursor();
						?>
					</select>
				</p>
				<p>  Equipe 2 : </p>
				<!-- Liste des équipes -->
				<p>
					<select class="w3-select" name="equipe2" id="equipe2" required>
						<?php
						$reponse = $bdd->query('SELECT id_equipe, nom FROM equipe WHERE id_equipe IN (SELECT DISTINCT(id_equipe) FROM flight WHERE id_comp = ' . $_GET["id_comp"] . ') ORDER BY nom;');
						while ($lstEquipe = $reponse->fetch())
						{ echo '<option value="' . $lstEquipe['id_equipe'] . '">' . $lstEquipe['nom'] . '</option>' ;}
						$reponse->closeCursor();
						?>
					</select>
				</p>
				
				<!-- Permuter -->
				<button onClick="permuterFly();" type="submit" class="w3-button w3-block w3-padding-large ps-color w3-margin-bottom">Permuter Fly</button>
				
				<!-- Listing du fly  -->
				<?php
					// Calcul de l'entête 'Par' et 'Formule de jeu'
					$tdPar = '';
					$tdFormule = '';
					$lstPar = $bdd->query('SELECT t.par, f.nomabr FROM trou t, ref_formulejeu f WHERE t.id_comp = ' . $_GET["id_comp"] . ' AND t.id_formjeu = f.id_formjeu ORDER BY t.numero;');
					while ($parTrou = $lstPar->fetch())
					{ $tdPar     .= '<td>' . $parTrou['par'] . '</td>';
					  $tdFormule .= '<td>' . $parTrou['nomabr'] . '</td>';}
					$lstPar->closeCursor();
					
					$l_numFly    = 0;
					$l_numEquipe = 0;
					$reponse = $bdd->query('SELECT f.numero, e.id_equipe, e.nom AS eqp_nom, c.logo, j.prenom, j.nom FROM `flight` f, `equipe` e, `club` c, `joueur` j WHERE f.id_comp = ' . $_GET["id_comp"] . ' AND f.id_equipe = e.id_equipe AND e.id_club = c.id_club AND f.id_joueur = j.id_joueur ORDER BY f.numero, f.id_equipe ');
					while ($listeFly = $reponse->fetch())
					{ 
						// Début Fly
						if ($l_numFly <> $listeFly['numero'])
						{
							if ($l_numFly <> 0) { echo '</table><br></div>'; }
							$l_numFly = $listeFly['numero'];

							echo '<div style="page-break-before: always;">
									<table class="w3-table">
									<tr>
										<td width="17%" class="sansBord"><h3>Fly ' . $listeFly['numero'] . '</h3></td><td width="17%" class="sansBord"></td><td width="17%" class="sansBord">Trou</td>';
										
							for ($idTrou = 1 ; $idTrou <= $nbTrou ; $idTrou++)
							{ echo '<td>'.$idTrou.'</td>'; }
						
							echo '</tr>
									<tr><td class="sansBord"></td><td class="sansBord"></td><td class="sansBord">Par</td>' . $tdPar . '</tr>
									<tr><td class="sansBord"></td><td class="sansBord"></td><td class="sansBord">Formule</td>' . $tdFormule . '</tr>';	
						}
						
						// Liste des joueurs de l'équipe
						// Si nouvelle équipe
						if ($l_numEquipe <> $listeFly['id_equipe'])
						{	echo '<tr><td rowspan="'.$l_nbJouParEqp.'">' . $listeFly['eqp_nom'] . '<br><img src="//img/clubs/' . $listeFly['logo'] . '"></td>'; 
							$l_numEquipe = $listeFly['id_equipe'];
						}
						else
						{	echo '<tr>'; }
						
						echo '<td class="ps-prenom">' . $listeFly['prenom'] . '</td><td class="ps-nom">' . $listeFly['nom'] . '</td>';

						for ($idTrou = 1 ; $idTrou <= $nbTrou ; $idTrou++)
						{ echo '<td></td>'; }

						echo '</tr>';
						
					}
					echo '</table></div>';
					$reponse->closeCursor();
				?>
				
				<p id="resultat">--</p>

			</div>
		</div>
		
		<?php include("../../php/body_down.php");  ?>
		
			
		<script>
			// Valide les joueurs de l'étape
			function permuterFly()
			{
				// Déclaration des variables
				var l_idComp    = "";
								
				// Calcul des variables 
				l_idComp = document.getElementById("idComp").value;
				l_idEquipe1 = document.getElementById("equipe1").value;
				l_idEquipe2 = document.getElementById("equipe2").value;
			
				// Récupération des détails du trou dans la base sgc
				var obj, dbParam, xmlhttp, myObj, x, txt = "";
				obj = { "idComp":l_idComp, "equipe1":l_idEquipe1, "equipe2":l_idEquipe2};
				dbParam = JSON.stringify(obj);
				xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						location.reload();
					}
				};
				xmlhttp.open("POST", "//site/v0/json/permutationFly.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send("x=" + dbParam);
			}
			
		</script>
	</body>
</html>
