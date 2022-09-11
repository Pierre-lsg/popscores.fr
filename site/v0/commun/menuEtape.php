<?php include("../../php/connectdb.php");  ?>
<?php include("commun/calculMenu.php");  ?>
<!-- Sidebar/menu -->
<nav class="w3-sidebar ps-color_sec w3-collapse w3-top w3-large w3-padding" style="z-index:3;width:300px;font-weight:bold;" id="mySidebar"><br>
	<a href="javascript:void(0)" onclick="w3_close()" class="w3-button w3-hide-large w3-display-topleft" style="width:100%;font-size:22px">Fermer</a>
	<div class="w3-container">
		<img src="//img/logo1_8.png" alt="logo Pop Scores" class="w3-display-topmiddle">
		<!--<h3 style="padding-top:100px!important;padding-bottom:64px!important"><b>POP Scores</b></h3>-->
	</div>
		<div class="w3-bar-block" style="padding-top:162px!important;">
			<?php if($parcours_ok) { echo '<a href="//site/v0/parcours.php?id_comp='.$_GET['id_comp'].'" class="w3-bar-item w3-button ps-hover-color" id="navParcours" style="padding-top:20px!important;">';} 
			else { echo '<a href="#" class="w3-bar-item" id="navParcours" style="padding-top:20px!important;">';} ?>
			Parcours</a> 
			<?php if($joueurs_ok) { echo '<a href="//site/v0/joueurs.php?id_comp='.$_GET['id_comp'].'" class="w3-bar-item w3-button ps-hover-color" id="navParcours" style="padding-top:20px!important;">';} 
			else { echo '<a href="#" class="w3-bar-item" id="navParcours" style="padding-top:20px!important;">';} ?>
			Joueurs</a> 
			<?php if($demarrer_ok) { echo '<a href="//site/v0/demarrer.php?id_comp='.$_GET['id_comp'].'" class="w3-bar-item w3-button ps-hover-color" id="navParcours" style="padding-top:20px!important;">';} 
			else { echo '<a href="#" class="w3-bar-item" id="navParcours" style="padding-top:20px!important;">';} ?>
			Démarrer</a> 
			<?php if($saisieSc_ok) { echo '<a href="//site/v0/saisirScores.php?id_comp='.$_GET['id_comp'].'" class="w3-bar-item w3-button ps-hover-color" id="navParcours" style="padding-top:20px!important;">';} 
			else { echo '<a href="#" class="w3-bar-item" id="navParcours" style="padding-top:20px!important;">';} ?>
			Saisir les scores</a> 
			<?php if($publieSc_ok) { echo '<a href="//site/v0/publierScores.php?id_comp='.$_GET['id_comp'].'" class="w3-bar-item w3-button ps-hover-color"  style="padding-top:20px!important;" id="navParcours">';} 
			else { echo '<a href="#" class="w3-bar-item" id="navParcours" style="padding-top:20px!important;" style="padding-top:20px!important;">';} ?>
			Publier les Scores</a> 
			<hr>
			<a href="//site/v0/etape.php?id_comp=<?php echo $_GET['id_comp']?>" onclick="w3_close()" class="w3-bar-item w3-button ps-hover-color">Accueil Etape</a> 
			<a href="//site/v0/index.php" onclick="w3_close()" class="w3-bar-item w3-button ps-hover-color" style="padding-top:20px!important;">Retour</a> 
			<hr>
			<a href="//site/v0/parametres_etape.php?id_comp=<?php echo $_GET['id_comp']?>" class="w3-bar-item w3-button ps-hover-color" style="padding-top:20px!important;">Paramètres</a> 
			<a href="//site/v0/logout.php" onclick="w3_close()" class="w3-bar-item w3-button ps-hover-color" style="padding-top:20px!important;">Se déconnecter</a> 
		</div>
</nav>

<!-- Top menu on small screens -->
<header class="w3-container w3-top w3-hide-large ps-color w3-xlarge w3-padding">
	<a href="javascript:void(0)" class="w3-button ps-color w3-margin-right" onclick="w3_open()">☰</a>
	<span>POP Scores</span>
	<img src="//img/logo_1-16.png" alt="logo Pop Scores" class="w3-display-right">
</header>

<!-- Overlay effect when opening sidebar on small screens -->
<div class="w3-overlay w3-hide-large" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="myOverlay"></div>
