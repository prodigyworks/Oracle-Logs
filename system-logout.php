<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	$qry = "UPDATE ols_members SET " .
			"status = 'N' " .
			"WHERE member_id = " . $_SESSION['SESS_MEMBER_ID'] . "";
	$result = mysql_query($qry);
	
	//Unset the variables stored in session
	unset($_SESSION['SESS_MEMBER_ID']);
	unset($_SESSION['SESS_FIRST_NAME']);
	unset($_SESSION['SESS_LAST_NAME']);
	unset($_SESSION['ROLES']);
	unset($_SESSION['MENU_CACHE']);
	unset($_SESSION['breadcrumb']);
	unset($_SESSION['breadcrumbPage']);
	
	header("location: index.php");
	
?>
