<?php 
	require_once("system-db.php");
	
	start_db();
	initialise_db();
	
	if (! isAuthenticated()) {
		header("location: system-register.php");
	}
	
	header("location: askquestion.php?requestchat=true");
?>
