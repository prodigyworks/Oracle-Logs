<?php
	include("system-header.php"); 

	$header = new QuotationHeader();
	$header->load($_GET['id']);
?>
<h2>Quotation <a title="View Quote" href="viewquote?id=<?php echo $header->headerid; ?>"><?php echo $header->prefix . sprintf("%04d", $header->headerid); ?></a> has been saved.</h2>

<?php
	if ($header->status == "N" || $header->status == "R") {
?>
<h3>This is currently in "<i>Draft</i>" mode and has not been sent for approval.</h3>
<?php		
	} else {
?>
<h3>Notifications have been sent regarding changes to this job.</h3>
<?php
	}
?>


<a class='backicon' href='index.php' title="Dashboard"></a>
<?php
	include("system-footer.php"); 
?>