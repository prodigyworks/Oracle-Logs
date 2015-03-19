<?php 
	require_once("quotationitem.php");
	require_once("system-db.php");
	
	start_db();
	initialise_db();

	$header = $_SESSION['QUOTATION'];
	$newHeader = $header->cancel();
	
	unset($_SESSION['QUOTATION']);
	
	if ($newHeader != null) {
		header("location: cancelledthrowawayquote.php?id=" . $newHeader->headerid);	
		
	} else {
		header("location: cancelledquote.php?id=" . $header->headerid);	
	}
		

?>
