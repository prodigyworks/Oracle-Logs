<?php
	include("system-header.php"); 
	
	$id = base64_decode($_GET['id']);	
	$qry = "UPDATE ols_members SET articlealerts = 0 WHERE member_id = $id";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
?>
<h4>Successfully unsubscribed.</h4>
<?php
	include("system-footer.php"); 
?>
