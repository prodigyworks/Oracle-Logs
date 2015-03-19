<?php
	include("system-header.php"); 
	
	$search = null;
	
	if (isset($_POST['searchpanel'])) {
		$search = $_POST['searchpanel'];
	}
	
	$fromrow = 0;
	$torow = 20;
	
	if (isset($_GET['from'])) {
		$fromrow = $_GET['from'];
	}
	
	if (isset($_GET['to'])) {
		$torow = $_GET['to'];
	}
	
	$pagesize = ($torow - $fromrow);
?>
<div class='articles'>
	<h2>Search</h2>
	<br>
	<table width=100% class='grid list' id="xx" cellspacing=0 cellpadding=0>
	    <thead>
	      <tr>
	        <td width='20px'>&nbsp;</td>
	        <td width='20px'>&nbsp;</td>
	        <td width='80px'>Date</td>
	        <td>Title</td>
	        <td width='86px'>Author</td>
	        <td width='80px' align=right>Comments</td>
	        <td width='80px'>Live</td>
	        <td width='80px'>Expires</td>
	        <td width='20px'>&nbsp;</td>
	      </tr>
	    </thead>
	    <tbody>
	    	<?php
	    		$row = 1;
	    		$nextpage = false;
	    		$prevpage = ($fromrow > 0);
	    		$endrow = $torow + 1;
	    		$qry = "SELECT DISTINCT 'Q' AS doctype,  A.publishedrole, A.id, A.title,  " .
	    				"DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, " .
	    				"DATE_FORMAT(A.publisheddate, '%d/%m/%Y') AS publisheddate, " .
	    				"DATE_FORMAT(A.expirydate, '%d/%m/%Y') AS expirydate, " .
						"(SELECT COUNT(*) FROM ols_questionanswers X  WHERE X.questionid = A.id AND published = 'Y') AS answered, " .
	    				"B.login " .
	    				"FROM ols_question A " .
	    				"INNER JOIN ols_members B " .
	    				"ON B.member_id = A.memberid " .
	    				"WHERE A.published = 'Y' " .
	    				"AND A.publisheddate <= NOW() " .
	    				"AND A.expirydate >= NOW() " .
						"AND MATCH (A.title, A.body) AGAINST('$search' IN BOOLEAN MODE) " .
						
						"UNION " .
						"SELECT DISTINCT 'Q' AS doctype,  A.publishedrole, A.id, A.title, " .
						"DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, " .
						"DATE_FORMAT(A.publisheddate, '%d/%m/%Y') AS publisheddate, " .
						"DATE_FORMAT(A.expirydate, '%d/%m/%Y') AS expirydate, " .
						"(SELECT COUNT(*) FROM ols_questionanswers X  WHERE X.questionid = A.id AND published = 'Y') AS answered, " .
						"B.login " .
						"FROM ols_question A " .
						"INNER JOIN ols_members B " .
						"ON B.member_id = A.memberid " .
						"INNER JOIN ols_questionanswers C " .
						"ON C.questionid = A.id " .
						"WHERE A.published = 'Y'  " .
						"AND A.publisheddate <= NOW() " .
						"AND A.expirydate >= NOW() " .
						"AND MATCH (C.body) AGAINST('$search' IN BOOLEAN MODE) " .  
						
						"UNION " .
						"SELECT DISTINCT 'Q' AS doctype,  A.publishedrole, A.id, A.title, " .
						"DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, " .
						"DATE_FORMAT(A.publisheddate, '%d/%m/%Y') AS publisheddate, " .
						"DATE_FORMAT(A.expirydate, '%d/%m/%Y') AS expirydate, " .
						"(SELECT COUNT(*) FROM ols_questionanswers X  WHERE X.questionid = A.id AND published = 'Y') AS answered, " .
						"B.login " .
						"FROM ols_question A " .
						"INNER JOIN ols_members B " .
						"ON B.member_id = A.memberid " .
						"INNER JOIN ols_technology C " .
						"ON C.id = A.technologyid " .
						"WHERE A.published = 'Y'  " .
						"AND A.publisheddate <= NOW() " .
						"AND A.expirydate >= NOW() " .
						"AND MATCH (C.name) AGAINST('$search' IN BOOLEAN MODE) " .  
						
						"UNION " .
						"SELECT DISTINCT 'Q' AS doctype,  A.publishedrole, A.id, A.title, " .
						"DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, " .
						"DATE_FORMAT(A.publisheddate, '%d/%m/%Y') AS publisheddate, " .
						"DATE_FORMAT(A.expirydate, '%d/%m/%Y') AS expirydate, " .
						"(SELECT COUNT(*) FROM ols_questionanswers X  WHERE X.questionid = A.id AND published = 'Y') AS answered, " .
						"B.login " .
						"FROM ols_question A " .
						"INNER JOIN ols_members B " .
						"ON B.member_id = A.memberid " .
						"INNER JOIN ols_technology C " .
						"ON C.id = A.technologyid " .
						"INNER JOIN ols_technologyversion D " .
						"ON D.id = A.technologyversionid " .
						"WHERE A.published = 'Y'  " .
						"AND A.publisheddate <= NOW() " .
						"AND A.expirydate >= NOW() " .
						"AND MATCH (D.name) AGAINST('$search' IN BOOLEAN MODE) " .  
						
						"UNION " .
						"SELECT DISTINCT 'Q' AS doctype,  A.publishedrole, A.id, A.title, " .
						"DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, " .
						"DATE_FORMAT(A.publisheddate, '%d/%m/%Y') AS publisheddate, " .
						"DATE_FORMAT(A.expirydate, '%d/%m/%Y') AS expirydate, " .
						"(SELECT COUNT(*) FROM ols_questionanswers X  WHERE X.questionid = A.id AND published = 'Y') AS answered, " .
						"B.login " .
						"FROM ols_question A " .
						"INNER JOIN ols_members B " .
						"ON B.member_id = A.memberid " .
						"INNER JOIN ols_operatingsystem C " .
						"ON C.id = A.opsystemid " .
						"INNER JOIN ols_operatingsystemversion D " .
						"ON D.id = A.opsystemversionid " .
						"WHERE A.published = 'Y'  " .
						"AND A.publisheddate <= NOW() " .
						"AND A.expirydate >= NOW() " .
						"AND MATCH (D.name) AGAINST('$search' IN BOOLEAN MODE) " .  
						
						"UNION " .
						"SELECT DISTINCT 'Q' AS doctype,  A.publishedrole, A.id, A.title, " .
						"DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, " .
						"DATE_FORMAT(A.publisheddate, '%d/%m/%Y') AS publisheddate, " .
						"DATE_FORMAT(A.expirydate, '%d/%m/%Y') AS expirydate, " .
						"(SELECT COUNT(*) FROM ols_questionanswers X  WHERE X.questionid = A.id AND published = 'Y') AS answered, " .
						"B.login " .
						"FROM ols_question A " .
						"INNER JOIN ols_members B " .
						"ON B.member_id = A.memberid " .
						"INNER JOIN ols_operatingsystem C " .
						"ON C.id = A.opsystemid " .
						"WHERE A.published = 'Y'  " .
						"AND A.publisheddate <= NOW() " .
						"AND A.expirydate >= NOW() " .
						"AND MATCH (C.name) AGAINST('$search' IN BOOLEAN MODE) " .  
						
						"UNION " .
						"SELECT DISTINCT 'A' AS doctype,  A.publishedrole, A.id, A.title, " .
						"DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, " .
						"DATE_FORMAT(A.publisheddate, '%d/%m/%Y') AS publisheddate, " .
						"DATE_FORMAT(A.expirydate, '%d/%m/%Y') AS expirydate, " .
						"-1 AS answered, " .
						"B.login " .
						"FROM ols_article A " .
						"INNER JOIN ols_members B " .
						"ON B.member_id = A.memberid " .
						"WHERE A.published = 'Y'  " .
						"AND A.publisheddate <= NOW() " .
						"AND A.expirydate >= NOW() " .
						"AND MATCH (A.title, A.body) AGAINST('$search' IN BOOLEAN MODE) " .  
	    				"ORDER BY 2 DESC " .
	    				"LIMIT $fromrow, $endrow";
				$result = mysql_query($qry);
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						if ($row++ > ($pagesize)) {
							$nextpage = true;
							break;
						};
						
						if ($member['doctype'] == "Q") {
							echo "<td width='20px'><img src='images/question.png'  title='Question' /></td>";
							
						} else {
							echo "<td width='20px'><img src='images/article.png'  title='Article' /></td>";
						}
						
						if (isUserInRole($member['publishedrole']) || isUserInRole("CONSULTANT")) {
							echo "<td width='20px'><img src='images/allowed.png'  title='Permission Allowed' /></td>";
						
						} else {
							echo "<td width='20px'><img src='images/denied.png' title='Preview Only' /></td>";
						}
						echo "<td>" . $member['createddate'] . "</td>";
						
						if ($member['doctype'] == "Q") {
							echo "<td><a href='viewquestion.php?id=" . $member['id'] . "'>" . $member['title'] . "</a></td>";
						
						} else {
							echo "<td><a href='viewarticle.php?id=" . $member['id'] . "'>" . $member['title'] . "</a></td>";
						}
						
						echo "<td width='86px'>" . $member['login'] . "</td>";
						
						if ($member['answered'] < 0) {
						echo "<td align=right width='80px'>&nbsp;</td>";
							
						} else {
							echo "<td align=right width='80px'>" . $member['answered'] . "</td>";
						}
						echo "<td width='80px'>" . $member['publisheddate'] . "</td>";
						echo "<td width='80px'>" . $member['expirydate'] . "</td>";
						
						if ($member['doctype'] == "Q") {
							echo "<td width='20px'><a href='viewquestion.php?id=" . $member['id'] . "'><img src='images/view.png'  title='View question' /></a></td>";
						
						} else {
							echo "<td width='20px'><a href='viewarticle.php?id=" . $member['id'] . "'><img src='images/view.png'  title='View question' /></a></td>";
						}
						
						echo "</tr>\n";
					}
				} else {
					logError($qry . " = " . mysql_error());
				}
	    	?>
	    </tbody>
	</table>
	
	<?php
		if ($prevpage) {
			echo "<img src='images/previouspage.png' onclick='window.location.href = \"" . $_SERVER['PHP_SELF'] . "?from=" . ($fromrow - $pagesize) . "&to=" . ($torow - $pagesize) . "\"' />";
		}
		
		if ($nextpage) {
			echo "<img src='images/nextpage.png' onclick='window.location.href = \"" . $_SERVER['PHP_SELF'] . "?from=" . ($fromrow + $pagesize) . "&to=" . ($torow + $pagesize) . "\"' />";
		}
	?>
</div>
<?php
	include("system-footer.php"); 
?>