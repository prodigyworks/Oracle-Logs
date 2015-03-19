<?php
	include("system-header.php"); 
	
	$advertid = $_GET['id'];	
	
	if (isUserInRole("ADMIN")) {
?>
<h4>The <a href='viewadvert.php?id=<?php echo $advertid; ?>'>advertisement</a> has been accepted and published.</h4>
<a class='backicon' href='index.php' title="Dashboard"></a>
<?php
		
	} else {
?>
<h4>Your <a href='viewadvert.php?id=<?php echo $advertid; ?>'>advertisement</a> has been accepted.<br>Details have been sent to our consultants for review.</h4>
<a class='backicon' href='index.php' title="Dashboard"></a>
<?php
	}
	
	include("system-footer.php"); 
?>
