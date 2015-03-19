<?php
	require_once("quotationitem.php");
	require_once("system-db.php");
	
	start_db();
	initialise_db();
	
	$quoteheaderid = $_GET['id'];

	$header = new QuotationHeader();
	$header->loadThrowAway($quoteheaderid);
	$header->headerid = 0;
	$header->originalthrowawayquoteid = $quoteheaderid;

	header("location: newquote.php")
?>