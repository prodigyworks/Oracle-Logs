sys<?php
	include("system-header.php"); 
	
	$id = base64_decode($_GET['id']);	
	$qry = "DELETE FROM ols_contacts WHERE id = $id";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
?>
<h4>Successfully unsubscribed.</h4>
<?php
	include("system-footer.php"); 
?>
