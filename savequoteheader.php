<?php 
	require_once("quotationitem.php");
	require_once("system-db.php");
	
	start_db();
	initialise_db();

	$header = $_SESSION['QUOTATION'];
	$header->save();
		
	unset($_SESSION['QUOTATION']);

	header("location: confirmedquote.php?id=" . $header->headerid);	
?>
