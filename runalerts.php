<?php
	session_start();
	
	$quietMode = true;
	date_default_timezone_set('Europe/London'); 
	
	require_once("quotationitem.php"); 

	if (! isset($_SESSION['SESS_MEMBER_ID'])) {
		require_once("system-db.php"); 
	
		$_SESSION['SESS_MEMBER_ID'] = "1";
		$_SESSION['SESS_FIRST_NAME'] = "Task";
		$_SESSION['SESS_LAST_NAME'] = "Manager";
		$_SESSION['ROLES'] = array();
		$_SESSION['ROLES'][0] = "PUBLIC";
		$_SESSION['ROLES'][1] = "ADMIN";
		
		start_db();
		initialise_db();
		
	} else {
		$quietMode = false;
		include("system-header.php"); 
	}
?>
<?php
   	sendRoleMessage("ADMIN", "Daily alert task schedule", "Information: Alerts task schedule run at " . date());
   	
	$qry = "SELECT A.*, DATE_FORMAT(A.createddate, '%Y-%m-%d %H:%i') AS createddate, " .
			"B.login, C.name AS sitename, A.customer as clientname, A.approvalid, " .
			"A.createdby, scheduledby, approvedby, completedby, qaby, archivedby, " .
			"DATE_FORMAT(A.approveddate, '%Y-%m-%d %H:%i') AS approveddate, " .
			"DATE_FORMAT(A.scheduleddate, '%Y-%m-%d %H:%i') AS scheduleddate, " .
			"DATE_FORMAT(A.ceapproveddate, '%Y-%m-%d %H:%i') AS ceapproveddate, " .
			"DATE_FORMAT(A.completeddate, '%Y-%m-%d %H:%i') AS completeddate, " .
			"DATE_FORMAT(A.approvalrequesteddate, '%Y-%m-%d %H:%i') AS approvalrequesteddate, " .
			"DATE_FORMAT(A.qadate, '%Y-%m-%d %H:%i') AS qadate," .
			"D.prefix AS jobprefix, D.id AS jobid " .
			"FROM jomon_quoteheader A " .
			"LEFT OUTER JOIN jomon_jobheader D " .
			"ON D.quoteid = A.id " .
			"INNER JOIN jomon_members B " .
			"ON B.member_id = A.createdby " .
			"INNER JOIN jomon_sites C " .
			"ON C.id = A.siteid " .
			"WHERE A.status NOT IN ( 'V', 'X', 'P', 'Q') " .
			"ORDER BY A.id";
	
	$result = mysql_query($qry);
	if (! $result) die("Error: " . mysql_error());
	
	//Check whether the query was successful or not
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
		    $prefix = $member['jobprefix'] != null ? $member['jobprefix'] : $member['prefix'];
		    $task = "";
		    $role = "";
		    
			if (($member['status'] == 'N' || $member['status'] == 'R')) {
				if ($member['approvalid'] == null) {
					continue;
				}
				
   				$alert = getAlert($member['approvalid'], $member['approveddate'], $member['approvalrequesteddate'], 1, 2);
			    $task = "Verification";
			    $role = "APPROVAL";
				
			} else if ($member['status'] == 'A') {
			    $alert = getAlert($member['scheduledby'], $member['scheduleddate'], $member['approveddate'], 1, 2);
			    $task = "Scheduling";
			    $role = "SCHEDULE";
			    
		    } else if ($member['status'] == 'S') {
			    $alert = getAlert($member['approvedby'], $member['ceapproveddate'], $member['scheduleddate'], 1, 2);
			    $task = "CE Approval";
			    $role = "CEAPPROVAL";
			    
		    } else if ($member['status'] == 'I') {
			    $alert = getAlert($member['ceapprovedby'], $member['completeddate'], $member['ceapproveddate'], 8, 10);
			    $task = "Completion";
			    $role = "COMPLETE";
			    
		    } else if ($member['status'] == 'C') {
			    $alert = getAlert($member['completedby'], $member['qadate'], $member['completeddate'], 2, 3);
			    $task = "Quality Assurance";
			    $role = "QA";
		    }
		    
		    if ($alert == "amber") {
		    	if (! $quietMode) {
			    	echo "<p>$task for job " . $prefix . sprintf("%04d", $member['id']) . " will be overdue shortly.</p>";
		    	}
		    	
		    	sendRoleMessage($role, "Daily Alert", "$task for job " . $prefix . sprintf("%04d", $member['id']) . " will be overdue shortly.");
		    
		    } else if ($alert == "red") {
		    	if (! $quietMode) {
			    	echo "<p>$task for job " . $prefix . sprintf("%04d", $member['id']) . " is overdue.</p>";
		    	}

		    	sendRoleMessage($role, "Daily Alert", "$task for job " . $prefix . sprintf("%04d", $member['id']) . " is overdue");
		    }
		}
	}
	
	function getAlert($checksum, $date1, $date2, $amberrule, $redrule) {
		$compareDate = new DateTime();
		
	    if ($checksum != null) {
	    	$compareDate = new DateTime($date1);
	    }

    	$resultDate = date_diff($compareDate, new DateTime($date2));
    	
    	if ($resultDate->format("%d") >= ($redrule)) {
			return "red";
			
    	} else if ($resultDate->format("%d") >= ($amberrule )) {
			return "amber";
			
		} else {
			return "green";
		}
	}
	
?>
<?php
   	if (! $quietMode) {
		include("system-footer.php");
   	} 
?>