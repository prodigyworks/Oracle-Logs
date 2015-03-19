<?php
	require_once("quotationitem.php");
	require_once("system-db.php");
	
	start_db();
	initialise_db();
	
	$quoteheaderid = $_GET['id'];

	$header = new QuotationHeader();
	$header->load($quoteheaderid);
	
	header("location: newquote.php")
?>