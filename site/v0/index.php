<?php include("../../php/before.php");  ?>

<!DOCTYPE html>
<html>
	<title>Pop Scores</title>
	<?php include("../../php/header.php");  ?>

	<body> 
		<?php include("../../php/connectdb.php");  ?>
		<?php include("commun/menu.php");  ?>

		<!-- !PAGE CONTENT! -->
		<div class="w3-main" style="margin-left:340px;margin-right:40px">

			<div style="text-align:center;margin-top:80px">
				<?php 
					if (isset($_SESSION['id_champ'])) {$l_idChamp = $_SESSION['id_champ']; }
					else {$l_idChamp = '5';} 
					
					if (isset($_SESSION['id_org'])) {$l_idOrg = $_SESSION['id_org']; }
					else {$l_idOrg = '2';} 
					
					$reponse = $bdd->query('SELECT c.nom, c.saison FROM championnat c WHERE c.id_champ = ' . $l_idChamp . ' ;');
					while ($donnees = $reponse->fetch())
					{ echo '<h1 class="w3-xxxlarge ps-text-color">' . $donnees['nom'] . " " . $donnees['saison'] . '</h1>'; }
					$reponse->closeCursor();
				?>

				<!-- Liste des championnats -->
				<table class="w3-table" id="tableCalculClass">
					<tr class="ps-color_sec" >
						<th>Etape</th>
						<th>Nom</th>
						<th>Date</th>
					</tr>
					<?php
					$reponse = $bdd->query('SELECT e.nom as nom_comp, e.etape, DATE_FORMAT(e.dateC,"%d/%m/%Y") AS dateC, e.id_comp FROM championnat c, competition e, organisation_comp oc WHERE c.id_champ = '. $l_idChamp .' AND c.id_champ = e.id_champ AND e.id_comp = oc.id_comp AND oc.id_org = '. $l_idOrg .' ORDER BY e.etape ');
					while ($donnees = $reponse->fetch())
					{ echo '<tr id="' . $donnees['id_comp'] . '" onmouseover="sltComp(this);" onmouseout="unSltComp(this);" onclick="accesComp(this)"><td>' . $donnees['etape'] . "</td><td>" . $donnees['nom_comp'] . '</td><td>' . $donnees['dateC'] . '</td><tr>'; }
					$reponse->closeCursor();
					?>	
				</table>
			</div>
		</div>

		<?php include("../../php/body_down.php");  ?>
		<script>
			// Récupère et affiche le calcul de classement attendu
			function stockeComp(i_idComp) {
				localStorage.setItem("idComp", i_idcomp);
			}
			
			function accesComp(comp)
			{
				var link = "//site/v0/etape.php?id_comp="+comp.id;
				open(link,"_self")
			}

			function sltComp(obj) {	
				obj.setAttribute("class","ps-color");				
			}

			function unSltComp(obj) {	
				obj.setAttribute("class","");				
			}

			
		</script>
	
	</body>
</html>
