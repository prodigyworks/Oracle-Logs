<?php
	include("system-header.php"); 

	$header = new QuotationHeader();
	$header->loadThrowAway($_GET['id']);
?>
<h2>Quotation <a title="View Quote" href="viewthrowawayquote?id=<?php echo $header->headerid; ?>"><?php echo $header->prefix . sprintf("%04d", $header->headerid); ?></a> has been saved.</h2>
<h3>This is currently a "<i>Throw Away</i>" quote and has not been sent for approval.</h3>

<a class='backicon' href='index.php' title="Dashboard"></a>
<?php
	include("system-footer.php"); 
?>