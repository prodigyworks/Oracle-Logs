<?php 
	require_once("quotationitem.php");
	require_once("system-db.php");
	
	start_db();
	initialise_db();

	$header = $_SESSION['QUOTATION'];
	$header->save();
	$header->requestApproval();
		
	unset($_SESSION['QUOTATION']);

	header("location: processedquote.php?id=" . $header->headerid);	
?>
