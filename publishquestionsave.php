<?php
	include("system-header.php"); 
	
	$questionid = $_GET['id'];
	$expirydate = null;	
	$publisheddate = null;	
	
	$qry = "SELECT " .
			"DATE_FORMAT(A.expirydate, '%d/%m/%Y') AS expirydate, " .
			"DATE_FORMAT(A.publisheddate, '%d/%m/%Y') AS publisheddate " .
			"FROM ols_question A " .
			"WHERE A.id = $questionid";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$expirydate = $member['expirydate'];	
			$publisheddate = $member['publisheddate'];	
		}
	}
?>
<h4><a href='viewquestion.php?id=<?php echo $questionid; ?>'>Question</a> has been published.<br>This will be available between <?php echo $publisheddate; ?> and <?php echo $expirydate; ?>.</h4>
<a class='backicon' href='newquestions.php' title="Unpublished questions"></a>
<?php
	include("system-footer.php"); 
?>