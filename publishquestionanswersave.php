<?php
	include("system-header.php"); 
	
	$answerid = $_GET['id'];
	$expirydate = null;	
	$questionid = null;
	$publisheddate = null;	
	
	$qry = "SELECT " .
			"A.questionid, " .
			"DATE_FORMAT(A.expirydate, '%d/%m/%Y') AS expirydate, " .
			"DATE_FORMAT(A.publisheddate, '%d/%m/%Y') AS publisheddate " .
			"FROM ols_questionanswers A " .
			"WHERE A.id = $answerid";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$expirydate = $member['expirydate'];	
			$publisheddate = $member['publisheddate'];	
			$questionid = $member['questionid'];	
		}
	}
?>
<h4><a href='viewquestionanswer.php?questionid=<?php echo $questionid; ?>&id=<?php echo $answerid; ?>'>Answer</a> has been published.<br>This will be available between <?php echo $publisheddate; ?> and <?php echo $expirydate; ?>.</h4>
<a class='backicon' href='newquestionanswers.php' title="Unpublished answers"></a>
<?php
	include("system-footer.php"); 
?>
