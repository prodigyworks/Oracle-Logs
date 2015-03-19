<?php
	require_once("quotationitem.php");
	require_once("system-db.php");
	
	start_db();
	initialise_db();
	
	unset($_SESSION['QUOTATION']);
	
	header("location: newthrowawayquote.php")
?>