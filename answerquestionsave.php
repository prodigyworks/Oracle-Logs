<?php
	include("system-header.php"); 
	
	$questionid = $_GET['id'];	
	
	if (isUserInRole("CONSULTANT")) {
?>
<h4>Answer for <a href='viewquestion.php?id=<?php echo $questionid; ?>'>question</a> has been published.</h4>
<a class='backicon' href='index.php' title="Dashboard"></a>
<?php
	} else {
?>
<h4>Answer for <a href='viewquestion.php?id=<?php echo $questionid; ?>'>question</a> has been requested.<br>Details have been sent for approval.</h4>
<a class='backicon' href='index.php' title="Dashboard"></a>
<?php
	}
	include("system-footer.php"); 
?>
