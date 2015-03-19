<?php
	include("system-db.php");
	
	start_db();
	
	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
	
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}

	
	
	if (!$link) {
		logError('Failed to connect to server: ' . mysql_error());
	}
	 
	// make sure it's a genuine file upload
	if (is_uploaded_file($_FILES['cv']['tmp_name'])) {
		  // replace any spaces in original filename with underscores
		  $filename = str_replace(' ', '_', $_FILES['cv']['name']);
		  // get the MIME type 
		  $mimetype = $_FILES['cv']['type'];
		  
		  if ($mimetype == 'image/pjpeg') {
		    $mimetype= 'image/jpeg';
		  }
		
		 // upload if file is OK
		 if ($_FILES['cv']['size'] > 0) {
		     	
		   switch ($_FILES['cv']['error']) {
		     case 0:
		       // get the file contents
	
		      // Temporary file name stored on the server
		      $tmpName  = $_FILES['cv']['tmp_name'];  
		      $image = "";
		       
		      // Read the file 
		      $fp = fopen($tmpName, 'rb');
		      
			   while (!feof($fp)) {
			  	  $image .= fread($fp, 8192);
			   }
		      
		       fclose($fp);
	      
		       
		       // get the width and height
		       $size = $_FILES['cv']['size'];
		       $filename = $_FILES['cv']['name'];
		       $description = $_POST['title'];
		       $sessionid = session_id();
		       
	//	       mysql_real_escape_string
				$stmt = mysqli_prepare($link, "INSERT INTO ols_documents " .
						"(sessionid, name, filename, mimetype, image, size, createdby, createddate) " .
						"VALUES " .
						"(?, ?, ?, ?, ?, ?, ?, NOW())");
						
				if ( !$stmt) {   
					logError('mysqli error: '.mysqli_error($link)); 
				} 
				
				
				mysqli_stmt_bind_param($stmt, "sssssss", $sessionid, $description, $filename, $mimetype, $image, $size, $_SESSION['SESS_MEMBER_ID']);
	
			    if ( ! mysqli_stmt_execute($stmt)) {
					logError('mysqli error: '.mysqli_error($link)); 
			    }
	
	    		$documentid = $link->insert_id;
	    		$suffix = substr($_FILES['cv']['name'], lastIndexOf($_FILES['cv']['name'], "."));
	    		
		      // Read the file 
		      $fp = fopen($tmpName, 'rb');
		      $fw = fopen("uploads/cv_" . $documentid . $suffix, 'wb');
		      
			   while (!feof($fp)) {
			  	  fwrite($fw,  fread($fp, 8192));
			   }
		      
		       fclose($fp);
		       fclose($fw);
	    		
	          break;
	        case 3:
	        case 6:
	        case 7:
	        case 8:
	          	$result = "Error uploading $filename. Please try again.";
	          	break;
	        case 4:
	          	$result = "You didn't select a file to be uploaded.";
	      }
	      
	    } else {
	      	$result = "$filename is either too big or not an image.";
	    }
	}	
	
	initialise_db();

	if (isset($_POST['jobid'])) {
		$id = $_POST['jobid'];
		
		$qry = "SELECT B.firstname, B.website, B.lastname, B.imageid, A.* " .
			   "FROM ols_job A " .
			   "INNER JOIN ols_members B " .
			   "ON B.member_id = A.memberid " .
			   "WHERE A.id = $id";
		$result = mysql_query($qry);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				$html = "<h1>Job Application</h1>" .
						"<h3>Reference: " . $member['reference'] . "</h3>" .
						"<p>From: " . $_POST['firstname'] . " " . $_POST['lastname'] . " </p>" .
						"<p>Email: " . $_POST['email'] . " </p>" .
						"<p>Contact: " . $_POST['number'] . " </p>";
						
				sendUserMessage($member['memberid'], "Job Application", $html, "", array("uploads/cv_" . $documentid . $suffix));
				
				if (isAuthenticated()) {
					$memberid = $_SESSION['SESS_MEMBER_ID'];
	
					$qry = "INSERT INTO ols_jobapplications " .
							"(jobid, memberid, createddate, documentid) " .
							"VALUES " .
							"($id, $memberid, NOW(), $documentid)";
							
					$itemresult = mysql_query($qry);
					
					if (! $itemresult) {
						logError($qry . " = " . mysql_error());
					}
					
					sendUserMessage($memberid, "Job Application", $html);
				}
			}
		} else {
			logError($qry . " = " . mysql_error());
		}
	}

	header("location: jobapplyok.php"); 
?>