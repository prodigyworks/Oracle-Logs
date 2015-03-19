<?php 
	include("system-header.php");
	
	$qry = "SELECT A.id, A.requestsessionid, C.login, DATE_FORMAT(A.createddate, '%d/%m/%Y %T') AS createddate, A.questionid, D.title " .
			"FROM ols_chatsession A " .
			"INNER JOIN ols_members C " .
			"ON C.member_id = A.requestmemberid " .
			"INNER JOIN ols_question D " .
			"ON D.id = A.questionid " .
			"WHERE A.responsesessionid IS NULL " .
			"AND A.status = 'O' " .
			"ORDER BY A.id";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
?>
<table class='grid list' width='100%'>
	<thead>
		<tr>
			<td width='120px'>Requested By</td>
			<td width='120px'>When</td>
			<td>Subject</td>
			<td width='20px'>&nbsp;</td>
			<td width='20px'>&nbsp;</td>
		</tr>
	</thead>
<?php
	/* Show children. */
	while (($member = mysql_fetch_assoc($result))) {
?>
	<tr>
		<td width='120px'><?php echo $member['login']; ?></td>
		<td width='120px'><?php echo $member['createddate']; ?></td>
		<td><?php echo $member['title']; ?></td>
		<td width='20px'><img src='images/answer.png' title='Respond' onclick='window.location.href = "responseexpert.php?id=<?php echo $member['id']; ?>";'</td>
		<td width='20px'><img src='images/question.png' title='View question' onclick='window.location.href = "viewquestion.php?id=<?php echo $member['questionid']; ?>";'</td>
	</tr>
<?php
	}
?>
</table>
<!--  End of content -->
<?php include("system-footer.php") ?>
		