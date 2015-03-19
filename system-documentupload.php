<?php
	require_once('system-db.php');
	
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
	if (is_uploaded_file($_FILES['file']['tmp_name'])) {
	  // replace any spaces in original filename with underscores
	  $filename = str_replace(' ', '_', $_FILES['file']['name']);
	  // get the MIME type 
	  $mimetype = $_FILES['file']['type'];
	  
	  if ($mimetype == 'image/pjpeg') {
	    $mimetype= 'image/jpeg';
	  }
	
	 // upload if file is OK
	 if ($_FILES['file']['size'] > 0) {
	     	
	   switch ($_FILES['file']['error']) {
	     case 0:
	       // get the file contents

	      // Temporary file name stored on the server
	      $tmpName  = $_FILES['file']['tmp_name'];  
	      $image = "";
	       
	      // Read the file 
	      $fp = fopen($tmpName, 'rb');
	      
		   while (!feof($fp)) {
		  	  $image .= fread($fp, 8192);
		   }
	      
	       fclose($fp);
      
	       
	       // get the width and height
	       $size = $_FILES['file']['size'];
	       $filename = $_FILES['file']['name'];
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

    		$imageid = $link->insert_id;

		  	header("location: " . $_SERVER['HTTP_REFERER']);
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
    
    // if the form has been submitted, display result
	if (isset($result)) {
	  echo "<p><strong>$result</strong></p>";
	}
}
	
	       
 ?>