<?php 
	require_once("quotationitem.php");
	require_once("system-db.php");
	
	start_db();
	initialise_db();

	$header = $_SESSION['QUOTATION'];
	
	if (isset($_POST['siteid'])) $header->siteid = $_POST['siteid'];
	if (isset($_POST['customer'])) $header->customer = $_POST['customer'];
	if (isset($_POST['ccf'])) $header->ccf = $_POST['ccf'];
	if (isset($_POST['ccfpath'])) $header->ccfpath = $_POST['ccfpath'];
	if (isset($_POST['sungardpo'])) $header->sungardpo = $_POST['sungardpo'];
	if (isset($_POST['customerpo'])) $header->customerpo = $_POST['customerpo'];
	if (isset($_POST['cabinstalldate'])) $header->cabinstalldate = $_POST['cabinstalldate'];
	if (isset($_POST['contactid'])) $header->contactid = $_POST['contactid'];
	if (isset($_POST['requiredby'])) $header->requiredbydate = $_POST['requiredby'];
	if (isset($_POST['costcode'])) $header->costcode = $_POST['costcode'];
	if (isset($_POST['notes'])) $header->notes = $_POST['notes'];
		
	header("location: " . $_POST['forward']);	
?>
