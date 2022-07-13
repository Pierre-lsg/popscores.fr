<?php include("../../php/before.php");  ?>
<?php include("commun/before.php");  ?>

<!DOCTYPE html>
<html>
	<title>Déclarer joueurs pour une étape</title>
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
				<input style="visibility:hidden" id="idComp" value="<?php echo $_GET["id_comp"]; ?>">
				<?php 
				if (isset($_SESSION['id_champ'])) {$l_idChamp = $_SESSION['id_champ']; }
				else {$l_idChamp = '5';}
				?>				

				<!-- Liste des équipes -->
				<select class="w3-select" name="equipe" id="equipe" style="visibility:hidden">
					<?php
					$reponse = $bdd->query('SELECT id_equipe, nom FROM equipe ORDER BY nom;');
					while ($lstEquipe = $reponse->fetch())
					{ echo '<option value="' . $lstEquipe['id_equipe'] . '">' . $lstEquipe['nom'] . '</option>' ;}
					$reponse->closeCursor();
					?>
				</select>
				<table width="100%">
					<tr>
						<!-- Joueurs connus -->
						<td valign="top">
							<table class="w3-table" id="tblJoueurs">
								<tr class="ps-color_sec">
									<th colspan="5"><center><b>Joueurs connus</b></center></th>
								</tr>
								<tr class="ps-color_sec" >
									<th onclick="sortTable(0)">#</th>
									<th></th>
									<th onclick="sortTable(2)">Prénom</th>
									<th onclick="sortTable(3)">Nom</th>
									<th onclick="sortTable(4)"> Dernière équipe</th>
								<tr>
								<?php 
								$clrEquipe  = 'w3-light-gray';
								$equipeOdd  = false;
								$equipePrec = 0;
								$reponse = $bdd->query('SELECT j.id_joueur, j.nom, j.prenom, e.nom AS nom_eqp, e.id_equipe, e.estCalculChampionnat AS calcul FROM joueur j, equipe e WHERE j.id_equipe = e.id_equipe AND j.id_champ = ' . $l_idChamp . ' AND j.id_joueur NOT IN (SELECT DISTINCT c.id_joueur FROM joueur_comp c WHERE c.id_comp=' . $_GET["id_comp"] . ') ORDER BY id_equipe, prenom');
								while ($lstJoueur = $reponse->fetch())
								{ 								 	
									if ($lstJoueur['id_equipe'] <> $equipePrec) 
									{ 
										$equipePrec = $lstJoueur['id_equipe'];
										if ($equipeOdd == true) 
										{ 
											$equipeOdd = false;
											if ($lstJoueur['calcul']) {$clrEquipe  = 'ps-yellow';}
											else {$clrEquipe  = '';};
											 
										}
										else 
										{ 
											$equipeOdd = true;
											if ($lstJoueur['calcul']) {$clrEquipe  = 'ps-orange';}
											else {$clrEquipe  = 'w3-light-gray';};
										}
									}
								
									echo '<tr class="joueur '. $clrEquipe .'" onclick="sltJoueur(this)"><td>'. $lstJoueur['id_joueur'] .'</td><td></td><td class="ps-prenom" onclick="changePrenom(this,event)">' . $lstJoueur['prenom'] . '</td><td class="ps-nom" onclick="changeNom(this,event)">' . $lstJoueur['nom'] . '</td><td onclick="changeEquipe(this,event)" equipe="' . $lstJoueur['id_equipe'] . '">' . $lstJoueur['nom_eqp'] . '</td></tr>'; 
								}
								$reponse->closeCursor();
								?>
							</table>
						</td>
						
						<!-- Ajouts/ Suppression de joueurs -->
						<td valign="top" width='125px'>
							
							<div id="menu_joueur" style="position:fixed!important;left:auto;top:350px;">
								<b>
								<button type="button" class="w3-button w3-block w3-padding-large ps-color_sec w3-margin-bottom" onClick="ajouteLstJoueur()">&gt;</button>
								<button type="button" class="w3-button w3-block w3-padding-large ps-color_sec w3-margin-bottom" onClick="retireLstJoueur()">&lt;</button>
								<button type="button" class="w3-button w3-block w3-padding-large ps-color_sec w3-margin-bottom" onClick="nouveauJEC()">Nouveau</button>
								</b>
							</div>
						</td>
						
						<!-- Joueurs étapes -->
						<td valign="top">
							<table class="w3-table" id="tblJoueursComp">
								<tr class="ps-color_sec">
									<td colspan="5"><center><b>Joueurs Etape</b></center></td>
								</tr>
								<tr class="ps-color_sec">
									<td></td>
									<td></td>
									<td>Prénom</td>
									<td>Nom</td>
									<td> Dernière équipe</td>
								<tr>
								<?php 
								$clrEquipe  = 'w3-light-gray';
								$equipeOdd  = false;
								$equipePrec = 0;
								$estJoueurChamp = '';
//								$reponse = $bdd->query('SELECT j.id_joueur, j.nom, j.prenom, e.nom AS nom_eqp, j.estCalculChampionnat AS joueur_champ, e.id_equipe, e.estCalculChampionnat AS calcul FROM joueur j, equipe e WHERE j.id_equipe = e.id_equipe AND j.id_champ = ' . $l_idChamp . ' AND j.id_joueur IN (SELECT DISTINCT c.id_joueur FROM joueur_comp c WHERE c.id_comp=' . $_GET["id_comp"] . ') ORDER BY nom_eqp');
								$reponse = $bdd->query('SELECT j.id_joueur, j.nom, j.prenom, e.nom AS nom_eqp, j.estCalculChampionnat AS joueur_champ, e.id_equipe, e.estCalculChampionnat AS calcul FROM joueur j, joueur_comp c, equipe e WHERE c.id_comp = ' . $_GET["id_comp"] . ' AND j.id_joueur = c.id_joueur AND e.id_equipe = c.id_equipe AND j.id_champ = ' . $l_idChamp . ' ORDER BY nom_eqp');
								while ($lstJoueur = $reponse->fetch())
								{ 	
									if ($lstJoueur['id_equipe'] <> $equipePrec) 
									{ 
										$equipePrec = $lstJoueur['id_equipe'];
										if ($equipeOdd == true) 
										{ 
											$equipeOdd = false;
											if ($lstJoueur['calcul']) {$clrEquipe  = 'ps-yellow';}
											else {$clrEquipe  = '';};
											 
										}
										else 
										{ 
											$equipeOdd = true;
											if ($lstJoueur['calcul']) {$clrEquipe  = 'ps-orange';}
											else {$clrEquipe  = 'w3-light-gray';};
										}
									}
									
									if ($lstJoueur['joueur_champ'] <> 0) {$estJoueurChamp = '*';}
									else {$estJoueurChamp = '';}
									
									echo '<tr class="joueurComp '. $clrEquipe .'" onclick="sltJoueurComp(this)"><td>'. $lstJoueur['id_joueur'] .'</td><td></td><td>' . $estJoueurChamp . ' ' . $lstJoueur['prenom'] . '</td><td>' . $lstJoueur['nom'] . '</td><td equipe="' . $lstJoueur['id_equipe'] . '">' . $lstJoueur['nom_eqp'] . '</td></tr>'; 
								}
								$reponse->closeCursor();
								?>
							</table>
						</td>
					</tr>
				</table>
				
				<br><br>
				<!-- <button onClick="valideLstJoueur();" type="submit" class="w3-button w3-block w3-padding-large ps-color w3-margin-bottom">Valider</button> -->
				
				<p id="resultat">--</p>

			</div>
		</div>
		
		<?php include("../../php/body_down.php");  ?>
		
			
		<script>
			// Changer le nom
			function changePrenom(prenom,e)
			{
				var saisiePrenom ;
				
				// Arrêt de l'événément onClick
				e.stopPropagation();
				
				prenom.setAttribute("onclick","");
				saisiePrenom = document.createElement("input");
				saisiePrenom.type = "text";
				saisiePrenom.value = prenom.innerHTML;
				saisiePrenom.setAttribute("onBlur","validePrenom(this)");
				saisiePrenom.setAttribute("onclick","event.stopPropagation();")

				// attachement au champ <td> de l'input 
				prenom.innerHTML = '';
				prenom.appendChild(saisiePrenom);
			}
			
			function validePrenom(prenom)
			{
				var l_prenom    = prenom.value;
				var l_idJoueur = '';
				var prenomJoueur = prenom.parentNode;
				var joueur = prenomJoueur.parentNode;
				
				l_prenom = l_prenom.charAt(0).toUpperCase() + l_prenom.slice(1);;
				
				// Récupérer l'id du joueur
				l_idJoueur = joueur.childNodes[0].innerHTML;
				
				// Supprimer la cbb et replacer le nom de l'équipe
				prenomJoueur.removeChild(prenomJoueur.childNodes[0]);
				prenomJoueur.innerHTML = l_prenom;
				prenomJoueur.setAttribute("onclick","changePrenom(this,event)");
				
				// Appeler la fonction de modification
				var obj, dbParam, xmlhttp, myObj, x, txt = "";
				obj = { "idJoueur":l_idJoueur, "prenom":l_prenom};
				dbParam = JSON.stringify(obj);
				xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						document.getElementById("resultat").innerHTML = "OK";
					}
				};
				xmlhttp.open("POST", "/~popscores/site/v0/json/modifiePrenomJoueur.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send("x=" + dbParam);
			}
			
			// Changer le nom
			function changeNom(nom,e)
			{
				var saisieNom ;
				
				// Arrêt de l'événément onClick
				e.stopPropagation();
				
				nom.setAttribute("onclick","");
				saisieNom = document.createElement("input");
				saisieNom.type = "text";
				saisieNom.value = nom.innerHTML;
				saisieNom.setAttribute("onBlur","valideNom(this)");
				saisieNom.setAttribute("onclick","event.stopPropagation();")

				// attachement au champ <td> de l'input
				nom.innerHTML = '';
				nom.appendChild(saisieNom);
			}
			
			function valideNom(nom)
			{
				var l_nom    = nom.value;
				var l_idJoueur = '';
				var nomJoueur = nom.parentNode;
				var joueur = nomJoueur.parentNode;
				
				l_nom = l_nom.toUpperCase();
				
				// Récupérer l'id du joueur
				l_idJoueur = joueur.childNodes[0].innerHTML;
				
				// Supprimer la cbb et replacer le nom de l'équipe
				nomJoueur.removeChild(nomJoueur.childNodes[0]);
				nomJoueur.innerHTML = l_nom;
				nomJoueur.setAttribute("onclick","changeNom(this,event)");
				
				// Appeler la fonction de modification
				var obj, dbParam, xmlhttp, myObj, x, txt = "";
				obj = { "idJoueur":l_idJoueur, "nom":l_nom};
				dbParam = JSON.stringify(obj);
				xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						document.getElementById("resultat").innerHTML = "OK";
					}
				};
				xmlhttp.open("POST", "/~popscores/site/v0/json/modifieNomJoueur.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send("x=" + dbParam);
			}
			
			// Changer l'équipe
			function changeEquipe(equipe,e)
			{
				var listeEquipe = document.getElementById("equipe");
				var eqpJoueur   = listeEquipe.cloneNode(true);
				var l_ancEquipe = equipe.getAttribute("equipe");
				
				// Arrêt de l'événément onClick
				var evt = e ? e:window.event;
				evt.stopPropagation();
				
				equipe.setAttribute("onclick","");
				eqpJoueur.style.visibility = "visible";
				eqpJoueur.setAttribute("onBlur","valideEquipe(this)");
				
				// Calcul de l'ancien élément
				for (var i = 0, e = eqpJoueur.length; i < e; i++) 
				{
					if (eqpJoueur[i].nodeType === 1)
					{
						if (eqpJoueur.childNodes[i].value == l_ancEquipe)
						{
							eqpJoueur.selectedIndex = i - 1;
						}
					}
				}

				// attachement au champ <td> du clone de la cbb equipe
				equipe.innerHTML = '';
				equipe.appendChild(eqpJoueur);
			}
		
			// Valide la nouvelle équipe
			function valideEquipe(lstEquipe)
			{
				var l_idEquipe  = '';
				var l_nomEquipe = '';
				var l_idJoueur  = '';
				var nLstEquipe  = lstEquipe.childNodes;
				var equipe		= lstEquipe.parentNode;
				var joueur		= equipe.parentNode;
				
				// Récupérer l'id et le nom de l'équipe
				for (var i = 0, e = nLstEquipe.length; i < e; i++) 
				{
					if (nLstEquipe[i].nodeType === 1)
					{
						if (lstEquipe.childNodes[i].selected)
						{
							l_idEquipe  = lstEquipe.childNodes[i].value;
							l_nomEquipe = lstEquipe.childNodes[i].innerHTML;
						}
					}
				}					
								
				// Récupérer l'id du joueur
				l_idJoueur = joueur.childNodes[0].innerHTML;
				
				// Supprimer la cbb et replacer le nom de l'équipe
				equipe.removeChild(equipe.childNodes[0]);
				equipe.innerHTML = l_nomEquipe;
				equipe.setAttribute("equipe",l_idEquipe)
				equipe.setAttribute("onclick","changeEquipe(this,event)");
				
				// Appeler la fonction de modification
				var obj, dbParam, xmlhttp, myObj, x, txt = "";
				obj = { "idJoueur":l_idJoueur, "idEquipe":l_idEquipe};
				dbParam = JSON.stringify(obj);
				xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						document.getElementById("resultat").innerHTML = "Changement de l'équipe " + l_idJoueur + " " + l_nomEquipe;
					}
				};
				xmlhttp.open("POST", "/~popscores/site/v0/json/modifieEquipeJoueur.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send("x=" + dbParam);
			}
			
			// Créer un nouveau joueur
			function nouveauJEC()
			{
				// renvoi vers la page "Nouveau Joueur"
				var link = "/~popscores/site/v0/jec_gestion.php?id_comp="+document.getElementById("idComp").value;
				open(link,"_self");
			}
			
			// Définir une nouvelle équipe
			function nouvelleEquipe()
			{
				// renvoi vers la page "Nouvelle équipe"
				var link = "/~popscores/site/v0/equipe.php?id_comp="+document.getElementById("idComp").value;
				open(link,"_self");
			}
		
			// Sélection d'un joueur dans le tableau des joueurs
			function sltJoueur(obj) {				
				if (obj.childNodes[1].innerHTML != "X")
				{	obj.setAttribute("class","joueur slt-ligne");				
					obj.childNodes[1].innerHTML = "X";}
				else
				{	obj.setAttribute("class","joueur w3-white");				
					obj.childNodes[1].innerHTML = ""; }
			}
			
			// Sélection d'un joueur dans le tableau des joueurs de l'étape
			function sltJoueurComp(obj) {				
				if (obj.childNodes[1].innerHTML != "X")
				{	obj.setAttribute("class","joueurComp w3-light-blue");				
					obj.childNodes[1].innerHTML = "X";}
				else
				{	obj.setAttribute("class","joueurComp w3-white");				
					obj.childNodes[1].innerHTML = ""; }
			}
			
			// Déplace les joueurs sélectionnés dans l'étape
			function ajouteLstJoueur()
			{
				var tbl_Joueur  = document.getElementById("tblJoueurs");
				var tbl_JoueurC = document.getElementById("tblJoueursComp");
				var ligne       = document.getElementsByClassName("joueur");
				var ligneASupp;
				
				// Création dans le tableau 'tblJoueursComp'
				for (var i = 0, c = ligne.length; i < c; i++) 
				{			
					try
					{
						
						var cell = ligne[i].childNodes;
						
						if (cell[1].innerHTML == "X")
						{
							ligneC = tbl_JoueurC.insertRow(-1);
							ligneC.setAttribute("onclick","sltJoueurComp(this)");
							ligneC.setAttribute("class","joueurComp");


							cellC = ligneC.insertCell(0);
							cellC.innerHTML = cell[0].innerHTML;

							cellC = ligneC.insertCell(1);
							cellC.innerHTML = "";

							cellC = ligneC.insertCell(2);
							cellC.innerHTML = cell[2].innerHTML;

							cellC = ligneC.insertCell(3);
							cellC.innerHTML = cell[3].innerHTML;

							cellC = ligneC.insertCell(4);
							cellC.innerHTML = cell[4].innerHTML;
							cellC.setAttribute("equipe",cell[4].getAttribute("equipe"));
							cellC.setAttribute("onclick","changeEquipe(this,event)");
							
							// Suppression du tableau 'tblJoueurs'
							tbl_Joueur.deleteRow(ligne[i].rowIndex);
							i--;
						}
					}
					catch(e)
					{ }
				}
	
				valideLstJoueur() ;

			}
			
			// Retire les joueurs sélectionnés de l'étape
			function retireLstJoueur()
			{
				var tbl_Joueur  = document.getElementById("tblJoueurs");
				var tbl_JoueurC = document.getElementById("tblJoueursComp");
				var ligneC      = document.getElementsByClassName("joueurComp");
				
				for (var i = 0, c = ligneC.length; i < c; i++) 
				{
					try
					{
						var cellC = ligneC[i].childNodes;
					
						if (cellC[1].innerHTML == "X")
						{
							// Création dans le tableau 'tblJoueursComp'
							ligne = tbl_Joueur.insertRow(-1);
							ligne.setAttribute("onclick","sltJoueur(this)");
							ligne.setAttribute("class","joueur");						
							
							cell = ligne.insertCell(0);
							cell.innerHTML = cellC[0].innerHTML;

							cell = ligne.insertCell(1);
							cell.innerHTML = "";

							cell = ligne.insertCell(2);
							cell.innerHTML = cellC[2].innerHTML;

							cell = ligne.insertCell(3);
							cell.innerHTML = cellC[3].innerHTML;

							cell = ligne.insertCell(4);
							cell.innerHTML = cellC[4].innerHTML;
							cell.setAttribute("equipe",cellC[4].getAttribute("equipe"));
							cell.setAttribute("onclick","changeEquipe(this,event)");
						
							// Suppression du tableau 'tblJoueursComp'
							tbl_JoueurC.deleteRow(ligneC[i].rowIndex);
							i--;
						}
					}
					catch(e)
					{ }
				}
	
				valideLstJoueur() ;
			}
			
			// Valide les joueurs de l'étape
			function valideLstJoueur()
			{
				document.getElementById("resultat").innerHTML = "dde enreg";
				
				// Déclaration des variables
				var l_idComp    = "";
				var l_lstjoueur = "";
				var ligneC      = document.getElementsByClassName("joueurComp");
				
				if(ligneC.length != 0)
				{				
					// Calcul des variables 
					l_idComp = document.getElementById("idComp").value;
				
					for (var i = 0, c = ligneC.length; i < c; i++) 
					{	l_lstjoueur += ligneC[i].childNodes[0].innerHTML + "#" + ligneC[i].childNodes[4].getAttribute("equipe") + ",";}
					l_lstjoueur = l_lstjoueur.substr(0,l_lstjoueur.length - 1);
				

					// Récupération des détails du trou dans la base sgc
					var obj, dbParam, xmlhttp, myObj, x, txt = "";
					obj = { "id_comp":l_idComp, "listeJoueur":l_lstjoueur};
					dbParam = JSON.stringify(obj);
					xmlhttp = new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {
						if (this.readyState == 4 && this.status == 200) {
							// document.getElementById("resultat").innerHTML = "OK"
							// enregistrement mis à jour
							location.reload();
						}
					};
					xmlhttp.open("POST", "/~popscores/site/v0/json/enregJoueurCompet.php", true);
					xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xmlhttp.send("x=" + dbParam);
				}
				else {alert("Veuillez saisir des joueurs pour la compétition.");}
				
			}
			
			function sortTable(n) 
			{
				var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
				table = document.getElementById("tblJoueurs");
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
					for (i = 3; i < (rows.length - 1); i++) {
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
