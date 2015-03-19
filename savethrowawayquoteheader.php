<?php 
	require_once("quotationitem.php");
	require_once("system-db.php");
	
	start_db();
	initialise_db();

	$header = $_SESSION['QUOTATION'];
	$header->saveThrowAway();
		
	unset($_SESSION['QUOTATION']);

	header("location: confirmedthrowawayquote.php?id=" . $header->headerid);	
?>
