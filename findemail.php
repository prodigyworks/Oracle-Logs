<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	$email = $_POST['email'];
	$qry = null;
	$json = array(); 
	
	if (isset($_POST['login'])) {
		$qry = "SELECT * " .
				"FROM ols_members " .
				"WHERE email = '$email' AND member_id != " . $_POST['login'];
		
	} else {
		$qry = "SELECT * " .
				"FROM ols_members " .
				"WHERE email = '$email'";
	}
	
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$line = array("id"=>$member['member_id'], "name"=>$member['firstname'] . " " . $member['lastname'], "login"=>$member['login']);  
			
			array_push($json, $line);
		}
	}
	
	echo json_encode($json); 
?>