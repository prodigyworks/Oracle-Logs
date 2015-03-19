<?php
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$telephone = $_POST['telephone'];
	$email = $_POST['email'];
	
	if (isset($_POST['contactid']) && $_POST['contactid'] != "") {
		$id = $_POST['contactid'];
		$qry = "UPDATE ols_contacts SET " .
				"firstname = '$firstname', " .
				"lastname = '$lastname', " .
				"email = '$email', " .
				"telephone = '$telephone' " .
				"WHERE id = $id ";
		
	} else {
		$qry = "INSERT INTO ols_contacts " .
				"(firstname, lastname, email, telephone, createddate) " .
				"VALUES " .
				"('$firstname', '$lastname', '$email', '$telephone', NOW())";
	}
	
			
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	header("location: bulkemailalerts.php");
?>