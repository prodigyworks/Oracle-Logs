<?php 
	require_once("system-db.php");
	require_once("quotationitem.php");
	
	start_db();
	initialise_db();
		
	if (isset($_GET['title'])) {
		$_SESSION["PORTLET_" . $_GET['title']] = $_GET['item'];
	}
?>
