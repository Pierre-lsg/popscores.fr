<?php
try
{
	$bdd = new PDO('mysql:host=localhost;dbname=popscores', 'login', 'password');
}
catch(Exception $e)
{
		die('Erreur : '.$e->getMessage());
}
?>	
