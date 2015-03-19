<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	$pageid = $_GET['pageid'];
	
	$qry = "SELECT A.roleid " .
			"FROM ols_pageroles A " .
			"WHERE A.pageid = $pageid " .
			"ORDER BY A.roleid";
	$result = mysql_query($qry);
	$json = array(); 
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$line = array("id"=>$member['roleid'], "name"=>$member['roleid']);  
			
			array_push($json, $line);
		}
	}
	
	echo json_encode($json); 
?>