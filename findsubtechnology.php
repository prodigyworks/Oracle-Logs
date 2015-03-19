<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	$parentid = $_POST['parentid'];
	$qry = "SELECT * " .
			"FROM ols_technology " .
			"WHERE parentid = $parentid " .
			"ORDER BY name";
	$result = mysql_query($qry);
	$json = array(); 
	
	//Check whether the query was successful or not
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$line = array("id"=>$member['id'], "name"=>$member['name']);  
			
			array_push($json, $line);
		}
	}
	
	echo json_encode($json); 
?>