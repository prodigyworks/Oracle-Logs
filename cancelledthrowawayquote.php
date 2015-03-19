<?php
	include("system-header.php"); 
	
	$header = new QuotationHeader();
	$header->loadThrowAway($_GET['id']);
?>
<h2>Quotation <a title="View Quote" href="viewthrowawayquote?id=<?php echo $header->headerid; ?>"><?php echo $header->prefix . sprintf("%04d", $header->headerid); ?></a> has been cancelled.</h2>
<h2>This can be viewed within the <a href="listmycancelledquotes.php">List My Throw Away Quotes</a> menu option.</h2>

<a class='backicon' href='index.php' title="Dashboard"></a>
<?php
	include("system-footer.php"); 
?>