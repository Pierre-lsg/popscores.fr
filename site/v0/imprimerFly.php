<?php include("../../php/before.php");  ?>
<?php include("commun/before.php");  ?>

<!DOCTYPE html>
<html>
	<title>Imprimer Liste Fly</title>
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
					$nbTrou = 9;
		
					// Calcul des informations de la compétition : nombre de trou, ...
					$reponse = $bdd->query('SELECT `nbTrou`, `nbJouParEqp` FROM `competition` WHERE id_comp = ' . $_GET["id_comp"]);
					if ($reponse->rowCount() <> 0) 
					{ 
						$donnees  = $reponse->fetch();
						$nbTrou = $donnees['nbTrou'];
						$l_nbJouParEqp = $donnees['nbJouParEqp'];
					}
					$reponse->closeCursor();

					if (!isset($l_nbJouParEqp))
					{ $l_nbJouParEqp = '3'; }
					
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
					$reponse = $bdd->query('SELECT f.numero, e.id_equipe, e.nom AS eqp_nom, c.logo, j.prenom, j.nom FROM `flight` f, `equipe` e, `club` c, `joueur` j WHERE f.id_comp = ' . $_GET["id_comp"] . ' AND f.id_equipe = e.id_equipe AND e.id_club = c.id_club AND f.id_joueur = j.id_joueur ORDER BY f.numero, f.id_equipe  ;');
					while ($listeFly = $reponse->fetch())
					{ 
						// Début Fly
						if ($l_numFly <> $listeFly['numero'])
						{
							if ($l_numFly <> 0) { echo '</table><br></div>'; }
							$l_numFly = $listeFly['numero'];

							echo '<div style="page-break-after: always;">
									<table class="w3-table">
									<tr>
										<td width="17%" class="sansBord"><h3>Fly ' . $listeFly['numero'] . '</h3></td><td width="17%" class="sansBord"></td><td width="17%" class="sansBord">Trou</td>';
										
							for ($idTrou = 1 ; $idTrou <= $nbTrou ; $idTrou++)
							{ echo '<td>'.$idTrou.'</td>'; }
						
							echo '</tr>
									<tr><td class="sansBord"></td><td class="sansBord"></td><td class="sansBord">Par</td><center>' . $tdPar . '</center></tr>
									<tr><td class="sansBord"></td><td class="sansBord"></td><td class="sansBord">Formule</td><center>' . $tdFormule . '</center></tr>';	
						}
						
						// Liste des joueurs de l'équipe
						// Si nouvelle équipe
						if ($l_numEquipe <> $listeFly['id_equipe'])
						{	echo '<tr><td rowspan="'.$l_nbJouParEqp.'">' . $listeFly['eqp_nom'] . '<br><img src="/img/clubs/' . $listeFly['logo'] . '"></td>'; 
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

			</div>
		</div>
		
	</body>
</html>
