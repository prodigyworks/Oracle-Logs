<?php 
	require_once("system-db.php");
	require_once("quotationitem.php");
	
	start_db();
	initialise_db();
	
	echo "[\n";
	echo "{\n";
		
		
	if (isset($_GET['id'])) {
		$header = new QuotationHeader();
		$header->load($_GET['id']);
		
		echo "\"createddate\": \"" . $header->createddate . "\", \n";
		echo "\"createdby\": \"" . $header->createdby . "\" \n";
	}
	
	echo "\n}\n";
	echo "\n]\n";
?>
