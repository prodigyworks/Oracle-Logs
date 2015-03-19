<?php 
	require_once("quotationitem.php");
	require_once("system-db.php");
	
	start_db();
	initialise_db();

	$header = $_SESSION['QUOTATION'];
	
	if (isset($_POST['siteid'])) {
		$header->siteid = $_POST['siteid'];
	}
	
	$header->customer = $_POST['customer'];
	$header->notes = $_POST['notes'];

	header("location: newquoteitem.php");	
?>
