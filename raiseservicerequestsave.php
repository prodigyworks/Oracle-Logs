<?php
	include("system-header.php"); 
	
	$servicerequestid = $_GET['id'];	
?>
<h4>Your <a href='raiseservicerequest.php?id=<?php echo $servicerequestid; ?>'>service request</a> has been accepted.<br>Details have been sent to our consultants for review.</h4>
<a class='backicon' href='index.php' title="Dashboard"></a>
<?php
	include("system-footer.php"); 
?>
