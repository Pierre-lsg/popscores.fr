<?php include("../../php/connectdb.php");  ?>
<!-- Sidebar/menu -->
<nav class="w3-sidebar ps-color_sec w3-collapse w3-top w3-large w3-padding" style="z-index:3;width:300px;font-weight:bold;" id="mySidebar"><br>
	<a href="javascript:void(0)" onclick="w3_close()" class="w3-button w3-hide-large w3-display-topleft" style="width:100%;font-size:22px">Fermer</a>
	<div class="w3-container">
		<img src="//img/logo1_8.png" alt="logo Pop Scores" class="w3-display-topmiddle">
		<!--<h3 style="padding-top:100px!important;padding-bottom:64px!important"><b>POP Scores</b></h3>-->
	</div>
		<div class="w3-bar-block" style="padding-top:162px!important;">
			<a href="//site/arb_v0/logout.php" onclick="w3_close()" class="w3-bar-item w3-button ps-hover-color" style="padding-top:20px!important;">Se déconnecter</a> 
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
