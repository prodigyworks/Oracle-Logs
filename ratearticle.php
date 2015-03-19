<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	$id = $_POST['articleid'];
	$rating = $_POST['rating'];
	$memberid = getLoggedOnMemberID();
	$qry = "SELECT * " .
			"FROM ols_articlerating " .
			"WHERE articleid = $id " .
			"AND memberid = $memberid";
	$result = mysql_query($qry);
	$found = false;
	
	//Check whether the query was successful or not
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$found = true;
		}
		
	} else {
		die($qry . " - " . mysql_error());
	}
	
	if ($found) {
		$qry = "UPDATE ols_articlerating " .
				"SET rating = $rating " .
				"WHERE articleid = $id " .
				"AND memberid = $memberid";
				
	} else {
		$qry = "INSERT INTO ols_articlerating (" .
				"articleid, memberid, rating" .
				") VALUES (" .
				"$id, $memberid, $rating" .
				")";
	}
	
	$result = mysql_query($qry);
	
	if (! $result) {
		die($qry . " - " . mysql_error());
	}
	
	$qry = "SELECT AVG(rating) AS rating, COUNT(*) AS rows " .
			"FROM ols_articlerating " .
			"WHERE articleid = $id";		
	$result = mysql_query($qry);
	$json = array(); 
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$line = array("yourrating"=>"Rated by you : " . number_format($rating, 2), "average"=>$rating, "average"=>"Average rating : (" . number_format($member['rating'], 2) . " / 5.00) from " . $member['rows'] . " votes");  
			
			array_push($json, $line);
		}
	}
	
	echo json_encode($json); 
?>