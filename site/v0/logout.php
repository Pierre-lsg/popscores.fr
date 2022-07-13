<?php include("../../php/before.php");  ?>

<!DOCTYPE html>
<html>
	<title>Déconnexion ...</title>
	<body> 
		Déconnexion ...
		<?php 
		/*
			$_SESSION['connexion'] = '';
			$_SESSION['id_champ'] = '';
			$_SESSION['id_org'] = '';
		*/
			session_destroy(); 
		?>
		<script>
			var link = "/~popscores/index.php";
			open(link,"_self");
		</script>
	</body>
</html>
