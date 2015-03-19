<?php
	include("system-header.php"); 
	
	$questionid = $_GET['id'];	
?>
<h4>Your <a href='askquestion.php?id=<?php echo $questionid; ?>'>question</a> has been requested.<br>Details have been sent for approval.</h4>
<a class='backicon' href='index.php' title="Dashboard"></a>
<?php
	include("system-footer.php"); 
?>
