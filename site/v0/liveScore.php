<?php include("../../php/before.php");  ?>
<?php include("commun/before.php");  ?>

<!DOCTYPE html>
<html>
	<title>Pop Scores</title>
	<?php include("../../php/header.php");  ?>

	<body> 
		<?php include("../../php/connectdb.php");  ?>
		<?php include("commun/menuEtape.php");  ?>

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

				<!-- Live Score -->
				<table class="w3-table" id="tblStatsJoueurs">
					<tr class="ps-color_sec" >
						<th onclick="sortTable(0,'tblStatsJoueurs')">Equipe </th>
						<th onclick="sortTable(1,'tblStatsJoueurs')">Joueur </th>
						<th onclick="sortTable(2,'tblStatsJoueurs')">Trous réalisés </th>
						<th onclick="sortTable(3,'tblStatsJoueurs')">Score</th>
					</tr>
					<?php
					$joueurOdd = false;
					$l_Trous  = '';
					$clrJoueur = '';
					$l_scoreJoueur = '';
						
					$reponse = $bdd->query('SELECT e.nom AS nomEqp, j.prenom AS prenomJ, j.nom AS nomJ, j.id_joueur FROM joueur j, joueur_comp c, equipe e WHERE c.id_comp = ' . $_GET['id_comp'] . ' AND j.id_joueur = c.id_joueur AND j.id_equipe = e.id_equipe ;');
					while ($donnees = $reponse->fetch())
					{
						if ($joueurOdd == true) 
						{ $joueurOdd = false; $clrJoueur ='w3-light-gray'; }
						else { $joueurOdd = true; $clrJoueur = ''; }

						// Calcul des trous effectués
						$l_Trous = '';
						$repTrou = $bdd->query('SELECT t.numero FROM resultat r, trou t WHERE r.id_comp = ' . $_GET['id_comp'] . ' AND r.id_joueur = ' . $donnees['id_joueur'] . ' AND r.id_trou = t.id_trou ;');
						while ($lstTrous = $repTrou->fetch())
						{
							$l_Trous = $l_Trous . $lstTrous['numero'] . ', ';
						}
						$repTrou->closeCursor();
						if ($l_Trous != '') { $l_Trous = substr($l_Trous,0,-2);}
						
						// Calcul du score
						$l_scoreJoueur = '';
						$repScore = $bdd->query('SELECT SUM(score) AS scoreJoueur FROM resultat WHERE id_joueur = ' . $donnees['id_joueur'] . ' AND id_comp = ' . $_GET['id_comp'] . ' ;');
						while ($lstScore = $repScore->fetch())
						{
							$l_scoreJoueur = $lstScore['scoreJoueur'];
						}
						$repScore->closeCursor();
						
						// Affichage 
						echo '<tr class=" '. $clrJoueur .'"><td>' . $donnees['nomEqp'] . '</td><td>' . $donnees['prenomJ'] . ' ' . $donnees['nomJ'] . '</td><td>' . $l_Trous . '</td><td>' . $l_scoreJoueur . '</td></tr>';
					}
					$reponse->closeCursor();
					?>	
				</table>
				
				<br>
				
				<table class="w3-table" id="tblStatsEquipes">
					<tr class="ps-color_sec" >
						<th onclick="sortTable(0,'tblStatsEquipes')">Equipe </th>
						<th onclick="sortTable(1,'tblStatsEquipes')">Trous réalisés </th>
						<th onclick="sortTable(2,'tblStatsEquipes')">Score</th>
					</tr>
					<?php
					$equipeOdd = false;
					$l_Trous  = '';
					$clrEquipe = '';
						
					$reponse = $bdd->query('SELECT e.nom, j.id_equipe, SUM(r.score) AS points FROM equipe e, joueur_comp j, resultat r, club cl WHERE j.id_comp = ' . $_GET['id_comp'] . ' AND r.id_comp = ' . $_GET['id_comp'] . ' AND j.id_joueur = r.id_joueur AND e.id_equipe = j.id_equipe AND cl.id_club = e.id_club GROUP BY j.id_equipe');
					while ($donnees = $reponse->fetch())
					{
						if ($equipeOdd == true) 
						{ $equipeOdd = false; $clrEquipe ='w3-light-gray'; }
						else { $equipeOdd = true; $clrEquipe = ''; }

						// Calcul des trous effectués
						$l_Trous = '';
						$repTrou = $bdd->query('SELECT DISTINCT(t.numero) FROM resultat r, trou t, equipe e, joueur j WHERE r.id_comp = ' . $_GET['id_comp'] . ' AND e.id_equipe = ' . $donnees['id_equipe'] . ' AND r.id_trou = t.id_trou AND r.id_joueur = j.id_joueur AND j.id_equipe = e.id_equipe ;');
						while ($lstTrous = $repTrou->fetch())
						{
							$l_Trous = $l_Trous . $lstTrous['numero'] . ', ';
						}
						$repTrou->closeCursor();
						if ($l_Trous != '') { $l_Trous = substr($l_Trous,0,-2);}
						
						// Affichage 
						echo '<tr class=" '. $clrEquipe .'"><td>' . $donnees['nom'] . '</td><td>' . $l_Trous . '</td><td>' . $donnees['points'] . '</td></tr>';
					}
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
			
			// Tri la table de LiveScore Admin
			function sortTable(n, maTable) 
			{
				var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
				table = document.getElementById(maTable);
				switching = true;
				// Set the sorting direction to ascending:
				dir = "asc"; 
				/* Make a loop that will continue until
				no switching has been done: */
				while (switching) 
				{
					// Start by saying: no switching is done:
					switching = false;
					rows = table.getElementsByTagName("tr");
					/* Loop through all table rows (except the
					first, which contains table headers): */
					for (i = 1; i < (rows.length - 1); i++) {
						// Start by saying there should be no switching:
						shouldSwitch = false;
						/* Get the two elements you want to compare,
						one from current row and one from the next: */
						x = rows[i].getElementsByTagName("td")[n];
						y = rows[i + 1].getElementsByTagName("td")[n];
						/* Check if the two rows should switch place,
						based on the direction, asc or desc: */
						if (dir == "asc") {
							if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
								// If so, mark as a switch and break the loop:
								shouldSwitch= true;
								break;
							}
						} else if (dir == "desc") {
							if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
								// If so, mark as a switch and break the loop:
								shouldSwitch= true;
								break;
							}
						}
					}
					if (shouldSwitch) {
						/* If a switch has been marked, make the switch
						and mark that a switch has been done: */
						rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
						switching = true;
						// Each time a switch is done, increase this count by 1:
						switchcount ++; 
					} else {
						/* If no switching has been done AND the direction is "asc",
						set the direction to "desc" and run the while loop again. */
						if (switchcount == 0 && dir == "asc") {
							dir = "desc";
							switching = true;
						}
					}
				}
			}

		</script>
	
	</body>
</html>
