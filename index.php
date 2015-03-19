<?php
	include("system-header.php"); 
	
	if (isAuthenticated()) {
?>
<!--  Start of content -->
<h5><a href="writearticle.php">Write an article</a></h5>
<?php 
	}

	showAdvert(4, 700, 100); 
?>
<h4>Recent Questions</h4>
<div class='articles'>
	<table width=100% class='grid list' maxrows=18 id="xx" cellspacing=0 cellpadding=0>
	    <thead>
	      <tr>
	        <td width='20px'>&nbsp;</td>
	        <td width='80px'>Date</td>
	        <td>Title</td>
	        <td width='120px'>Author</td>
	        <td>Technology</td>
	        <td width='20px'>&nbsp;</td>
	      </tr>
	    </thead>
	    <tbody>
	    	<?php
	    		$qry = "SELECT DISTINCT A.id, A.title, " .
	    				"DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, " .
	    				"B.login, " .
	    				"C.name, " .
						"(SELECT COUNT(*) FROM ols_questionanswers X  WHERE X.questionid = A.id AND published = 'Y') AS answered " .
						"FROM ols_question A " . 
	    				"INNER JOIN ols_members B " .
	    				"ON B.member_id = A.memberid " .
	    				"INNER JOIN ols_technology C " .
	    				"ON C.id = A.technologyid " .
	    				"WHERE A.published = 'Y' " .
	    				"ORDER BY A.id DESC " .
	    				"LIMIT 10";
				$result = mysql_query($qry);
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						echo "<tr>\n";
						
						if ($member['answered'] > 0) {
							echo "<td><img src='images/answered.png' title='Answered' /></td>";
							
						} else {
							echo "<td><img src='images/unanswered.png' /></td>";
						}
						
						echo "<td>" . $member['createddate'] . "</td>";
						echo "<td><a href='viewquestion.php?id=" . $member['id'] . "'>" . $member['title'] . "</a></td>";
						echo "<td width='120px'>" . $member['login'] . "</td>";
						echo "<td>" . $member['name'] . "</td>";
						echo "<td width='20px'><a href='viewquestion.php?id=" . $member['id'] . "'><img src='images/view.png'  title='View article' /></a></td>";
						echo "</tr>\n";
					}
				} else {
					logError($qry . " = " . mysql_error());
				}
	    	?>
	    </tbody>
	</table>
</div>
<script>
	function slideSwitch() {
	    var $active = $('#slideshow IMG.active');
	
	    if ( $active.length == 0 ) $active = $('#slideshow IMG:last');
	
	    var $next =  $active.next().length ? $active.next()
	        : $('#slideshow IMG:first');
	
	    $active.addClass('last-active');
	
	    $next.css({opacity: 0.0})
	        .addClass('active')
	        .animate({opacity: 1.0}, 1000, function() {
	            $active.removeClass('active last-active');
	        });
	}
	
//	$(document).ready(function() {
//	    setInterval( "slideSwitch()", 5000 );
//	});
</script>

<?php include("system-footer.php"); ?>
