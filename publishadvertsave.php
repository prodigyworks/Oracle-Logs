<?php
	include("system-header.php"); 
	
	$advertid = $_GET['id'];	
	
?>
<h4>The <a href='viewadvert.php?id=<?php echo $advertid; ?>'>advertisement</a> has been accepted and published.</h4>
<a class='backicon' href='index.php' title="Dashboard"></a>
<?php
	include("system-footer.php"); 
?>
