<?php
	require_once("system-db.php");
	
	start_db();
	initialise_db();
	
	$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
	$imageid = 0;
	$result = null;
	
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}
	
	if (!$link) {
		logError('Failed to connect to server: ' . mysql_error());
	}
	
	define ('MAX_FILE_SIZE', 1024 * 500); 
	 
	// make sure it's a genuine file upload
	if (is_uploaded_file($_FILES['image']['tmp_name'])) {
	  // replace any spaces in original filename with underscores
	  $filename = str_replace(' ', '_', $_FILES['image']['name']);
	  // get the MIME type 
	  $mimetype = $_FILES['image']['type'];
	  
	  if ($mimetype == 'image/pjpeg') {
	    $mimetype= 'image/jpeg';
	  }
	  
	  // create an array of permitted MIME types
	  
	  $permitted = array('image/gif', 'image/jpeg', 'image/png', 'image/x-png');
	
	 // upload if file is OK
	 if (in_array($mimetype, $permitted)
	     && $_FILES['image']['size'] > 0
	     && $_FILES['image']['size'] <= MAX_FILE_SIZE) {
	     	
		   switch ($_FILES['image']['error']) {
		     case 0:
		       // get the file contents
	
		      // Temporary file name stored on the server
		      $tmpName  = $_FILES['image']['tmp_name'];  
		       
		      // Read the file 
		      $fp = fopen($tmpName, 'r');
		      $image = fread($fp, filesize($tmpName));
		      fclose($fp);
	      
		       
		       // get the width and height
		       $size = getimagesize($_FILES['image']['tmp_name']);
		       $width = $size[0];
		       $height = $size[1];
		       $binimage = file_get_contents($_FILES['image']['tmp_name']);
		       $image = mysql_real_escape_string($binimage);
		       $filename = $_FILES['image']['name'];
		       $description = $_POST['description'];
		       
	//	       mysql_real_escape_string
				$stmt = mysqli_prepare($link, "INSERT INTO ols_images " .
						"(description, name, mimetype, image, imgwidth, imgheight) " .
						"VALUES " .
						"(?, ?, ?, ?, ?, ?)");
						
				if ( !$stmt) {   
					logError('mysqli error: '.mysqli_error($link)); 
				} 
				
				
				mysqli_stmt_bind_param($stmt, "ssssss", $description, $filename, $mimetype, $binimage, $width, $height);
			   mysqli_stmt_execute($stmt);
	
	    		$imageid = $link->insert_id;
	
			   
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
	
	if ($result != null) {
		logError($result);
		exit();
	}
	
	$memberid = $_SESSION['SESS_MEMBER_ID'];
	$title = mysql_escape_string($_POST['title']);
	$groupid = $_POST['groupid'];
	$mysql_showfrom_date = substr($_POST['showfrom'], 6, 4 ) . "-" . substr($_POST['showfrom'], 3, 2 ) . "-" . substr($_POST['showfrom'], 0, 2 );
	$mysql_showto_date = substr($_POST['showto'], 6, 4 ) . "-" . substr($_POST['showto'], 3, 2 ) . "-" . substr($_POST['showto'], 0, 2 );
	$roleid = $_POST['roleid'];
	$url = $_POST['url'];
	$published = "N";
	
	if (isUserInRole("ADMIN")) {
		$published = "Y";
	}
	
	$qry = "INSERT INTO ols_advert " .
			"(title, groupid, url, createddate, publisheddate, expirydate, roleid, memberid, published, imageid) " .
			"VALUES " .
			"('$title', $groupid, '$url', NOW(), '$mysql_showfrom_date', '$mysql_showto_date', '$roleid', $memberid, '$published', $imageid)";
			
	$result = mysql_query($qry);
	$advertid = mysql_insert_id();
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	sendRoleMessage("ADMIN", "Verification required", "Verification required for advert " . $title);
	
	header("location:uploadadvertsave.php?id=$advertid");
?>
