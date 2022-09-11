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
					$reponse = $bdd->query('SELECT c.nom, c.saison, c.saison, e.nom as nom_comp, e.etape, e.id_comp, DATE_FORMAT(e.dateC,"%d/%m") AS dateC FROM championnat c, competition e WHERE e.id_comp = ' . $_GET["id_comp"] . ' AND c.id_champ = e.id_champ');
					while ($donnees = $reponse->fetch())
					{ echo '<h1 class="w3-xxxlarge ps-text-color">' . $donnees['nom'] . " " . $donnees['saison'] . " <br> #" . $donnees['etape'] . " " . $donnees['nom_comp'] . " - " . $donnees['dateC'] . '</h1>'; }
					$reponse->closeCursor();
				?>
				<center>
					<div>
						<a class="ps-lien-pop" style="width:50%" href="//site/v0/saisirScores_fly.php?id_comp=<?php echo $_GET['id_comp']?>">
							<span style="font-family: Calibri;">Saisie par Fly</span>
						</a> 	
						<a class="ps-lien-pop" style="width:50%" href="//site/v0/saisirScores_trou.php?id_comp=<?php echo $_GET['id_comp']?>">
							<span style="font-family: Calibri;">Saisie par Trou</span>
						</a> 	
						<a class="ps-lien-pop" style="width:50%" href="//site/v0/export/csv_resultatCompetition.php?id_comp=<?php echo $_GET['id_comp']?>">
							<span style="font-family: Calibri;">Export CSV</span>
						</a> 	
					</div>
				</center>
			</div>
		</div>
		
		<?php include("../../php/body_down.php");  ?>
	</body>
</html>
