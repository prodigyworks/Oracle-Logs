<?php
	include("system-header.php"); 
	
	$questionid = $_GET['id'];	
?>
<h4>Answer for <a href='viewservicerequest.php?id=<?php echo $questionid; ?>'>service request</a> has been responded.<br>A response has been sent to the user.</h4>
<a class='backicon' href='index.php' title="Dashboard"></a>
<?php
	include("system-footer.php"); 
?>
