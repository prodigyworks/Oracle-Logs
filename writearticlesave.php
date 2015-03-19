<?php
	include("system-header.php"); 
	
	if (isUserInRole("CONSULTANT")) {
	
?>
<h4>Article has been submitted and published.</h4>
<?php
	} else {
?>
<h4>Article has been submitted and will be verified for publication.</h4>
<?php
	}
	include("system-footer.php"); 
?>