<?php
	include("system-header.php"); 
?>
<h1>Premium Membership Cancelled</h1>
<script>
	if (window.parent) {
		window.parent.location.href = "premiummemberconfirmcancel.php";
		
	} else {
		window.location.href = "premiummemberconfirmcancel.php";
	}
</script>
<?php
	include("system-footer.php"); 
?>