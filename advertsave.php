<?php
	include("system-header.php"); 
	
	$advertid = $_GET['id'];	
?>
<h4>Verification for <a href='viewadvert.php?id=<?php echo $advertid; ?>'>advertisement</a> has been requested.<br>Details have been sent for approval.</h4>
<a class='backicon' href='index.php' title="Dashboard"></a>
<?php
	include("system-footer.php"); 
?>
