<?php
	include("system-header.php"); 

	$header = new QuotationHeader();
	$header->load($_GET['id']);
?>
<h2>Quotation <a title="View Quote" href="viewquote?id=<?php echo $header->headerid; ?>"><?php echo $header->prefix . sprintf("%04d", $header->headerid); ?></a> has been confirmed.</h2>
<h3>And has been sent for approval.</h3>
<br>
<div class="approvers">
<span>Possible Approvers</span>

<?php
	$id = $header->approvalid;
	$qry = "SELECT B.firstname, B.lastname, B.member_id " .
			"FROM jomon_userroles A " .
			"INNER JOIN jomon_members B " .
			"ON B.member_id = A.memberid " .
			"WHERE roleid = '$id'";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			echo "<div class='item'><a href='profile?id=" . $member['member_id'] . "'>" . $member['firstname'] . " " . $member['lastname'] . "</a></div>";
		}
		
	} else {
		die($qry . " = " . mysql_error());
	}
?>
</div>
<br />
<a class='backicon' href='index.php' title="Dashboard"></a>
<?php
	include("system-footer.php"); 
?>