<?php include("../../php/before.php");  ?>
<?php include("commun/before.php");  ?>

<!DOCTYPE html>
<html>
	<title>Imprimer feuille de score par trou</title>
	<?php include("../../php/header.php");  ?>
	<style>
		td {border-style:solid;}
		.sansBord {border-style:none;}
	</style>
	
	<body> 
		<?php include("../../php/connectdb.php");  ?>

		<!-- !PAGE CONTENT! -->
		<div class="w3-main" style="margin-left:340px;margin-right:40px">

			<div style="text-align:center;margin-top:-25px">
				
				<!-- Champs cachés -->
				<!-- Identifiant de compétition -->
				<input style="visibility:hidden" id="idComp" value="<?php echo $_GET["id_comp"]; ?>">
				
				<!-- Définition du fly  -->
				<?php						
					// Calcul des informations de la compétition : nombre de joueurs par équipe
					$reponse = $bdd->query('SELECT `nbJouParEqp` FROM `competition` WHERE id_comp = ' . $_GET["id_comp"]);
					if ($reponse->rowCount() <> 0) 
					{ 
						$donnees  = $reponse->fetch();
						$l_nbJouParEqp = $donnees['nbJouParEqp'];
					}
					$reponse->closeCursor();

					if (!isset($l_nbJouParEqp))
					{ $l_nbJouParEqp = '3'; }

				
					// Calcul de l'entête 'Par' et 'Formule de jeu'
					$lstPar = $bdd->query('SELECT t.numero, t.par, f.nom FROM trou t, ref_formulejeu f WHERE t.id_comp = ' . $_GET["id_comp"] . ' AND t.id_formjeu = f.id_formjeu ORDER BY t.numero;');
					while ($parTrou = $lstPar->fetch())
					{ 					
						echo '<div style="page-break-after: always;">
							 <center><table class="w3-table w3-padding-small">
							 <tr>
								<td width="20%" class="sansBord"></td><td width="20%" class="sansBord"></td><td width="20%" class="sansBord">Trou</td><td width="20%">' . $parTrou['numero'] . '</td>
							 </tr>
							 <tr><td class="sansBord"></td><td class="sansBord"></td><td class="sansBord">Par</td><td>' . $parTrou['par'] . '</td></tr>
							 <tr><td class="sansBord"></td><td class="sansBord"></td><td class="sansBord">Formule</td><td>' . $parTrou['nom'] . '</td></tr>';

						$l_numFly    = 0;
						$l_numEquipe = 0;
						$reponse = $bdd->query('SELECT f.numero, e.id_equipe, e.nom AS eqp_nom, c.logo, j.prenom, j.nom FROM `flight` f, `equipe` e, `joueur` j, `club` c WHERE f.id_comp = ' . $_GET["id_comp"] . ' AND f.id_equipe = e.id_equipe AND e.id_club = c.id_club  AND f.id_joueur = j.id_joueur ORDER BY f.numero, f.id_equipe ;');


						while ($listeFly = $reponse->fetch())
						{ 
							// Début Fly
							if ($l_numFly <> $listeFly['numero'])
							{
								// if ($l_numFly <> 0) { echo '</table><br></div>'; }
								echo '<tr><td colspan="4"> Fly ' . $listeFly['numero'] . '</td></tr>';
								$l_numFly = $listeFly['numero'];
							}
							
							// Liste des joueurs de l'équipe
							// Si nouvelle équipe
							if ($l_numEquipe <> $listeFly['id_equipe'])
							{	echo '<tr><td rowspan="'.$l_nbJouParEqp.'">' . $listeFly['eqp_nom'] . '<br><img src="/img/clubs/' . $listeFly['logo'] . '"></td>'; 
								$l_numEquipe = $listeFly['id_equipe'];
							}
							else
							{	echo '<tr>'; }
							
							echo '<td class="ps-prenom">' . $listeFly['prenom'] . '</td><td class="ps-nom">' . $listeFly['nom'] . '</td>
							<td></td></tr>';
							
						}
						echo '</table></center></div>';
						$reponse->closeCursor();
					}
					$lstPar->closeCursor();
				?>

			</div>
		</div>
		
	</body>
</html>
