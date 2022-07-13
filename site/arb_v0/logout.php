<?php include("../../php/before.php");  ?>

<!DOCTYPE html>
<html>
	<title>Déconnexion ...</title>
	<body> 
		Déconnexion ...
		<?php 
			session_destroy(); 
		?>
		<script>
			var link = "/accueilArbitre.php";
			open(link,"_self");
		</script>
	</body>
</html>
