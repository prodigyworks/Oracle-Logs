<?php
	include("system-header.php"); 
	include("tinymce.php"); 
?>
<script src="js/jquery.rating.pack.js"></script>
<link href="css/jquery.rating.css" rel="stylesheet" type="text/css" />
<?php
	$articleid = $_GET['id'];
	$allowed = true;
	$owner = null;
	
	$qry = "SELECT A.id, A.publishedrole, A.title, A.body, A.tags, A.memberid, " .
			"DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, B.login " .
			"FROM ols_article A " .
			"INNER JOIN ols_members B " .
			"ON B.member_id = A.memberid " .
			"WHERE A.id = $articleid";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$allowed = isUserInRole($member['publishedrole']) || isUserInRole("CONSULTANT") || $member['memberid'] == getLoggedOnMemberID();
			$owner = $member['memberid'];

			echo "<h2>";
			
			if (! $allowed) {
				echo "<img src='images/denied.png' title='Read Only' />";
?>
<script>
	$(document).ready(function() {
			$("#body").css("height", "100px");
			$("#body").css("overflow", "hidden");
		});
</script>
<?php
			} else {
				echo "<img src='images/allowed.png' title='Permission Allowed' />";
			}
			
			echo "Title: " . $member['title'] . "</h2>";
			echo "<h4>Author : " . $member['login'] . "</h4>";
			echo "<h5>Posted on " . $member['createddate'] . "</h5>";
			echo "<br><hr><br><div id='body'>" . $member['body']. "</div>";
			
			if (! $allowed) {
				echo "<hr><h4>You do not have permission to view the whole document. Please upgrade</h4>";
			}
		}
		
	} else {
		logError($qry . " = " . mysql_error());
	}
	
	if ($allowed) {
		$qry = "SELECT B.* " .
				"FROM ols_articledocuments A " .
				"INNER JOIN ols_documents B " .
				"ON B.id = A.documentid " .
				"WHERE A.articleid = $articleid";
		$result = mysql_query($qry);
		$first = true;
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				if ($first) {
					$first = false;
					echo "<hr><p>Attached files. Click to view</p>";	
				}
				
				echo "<a target='_new' href='viewdocuments.php?id=" . $member['id'] ."'>" . $member['filename'] . "</a><br>";
			}
			
		} else {
			logError($qry . " = " . mysql_error());
		}
		
		if (isUserInRole("CONSULTANT")) {
			echo "<hr><h4>Comments</h4>";
			$qry = "SELECT B.login, A.body, A.createddate " .
					"FROM ols_articlecomments A " .
					"INNER JOIN ols_members B " .
					"ON B.member_id = A.memberid " .
					"WHERE A.articleid = $articleid";
			$result = mysql_query($qry);
			$first = true;
			
			if ($result) {
				while (($member = mysql_fetch_assoc($result))) {
					echo "<h5>Author : " . $member['login'] . "</h5>";
					echo "<h6>Posted : " . $member['createddate'] . "</h6>";
					
					echo "<div class='answer'>" . $member['body'] . "</div><br>";
					echo "<br><hr><br>";
				}
			} else {
				logError($qry . "-" . mysql_error());
			}
			
?>
		<br>
		<hr>
		<br>
		<form id="commentform" method="post" action="commentarticle.php?id=<?php echo $articleid; ?>">
			<textarea id="comment" name="comment" class="tinyMCE" cols=90 rows=5></textarea>
			<br>
			<div class="wrapper"><a class='link1' href="javascript:$('#commentform').submit();"><em><b>Comment on this article</b></em></a></div>
		</form<
<?php

		} else if (isAuthenticated() && $owner != getLoggedOnMemberID()) {
?>
		<br>
		<hr>
		<br>
		<div>
			<input name="rating" id="rating" type="radio" class="star {half: true}" value="1" />
			<input name="rating" id="rating" type="radio" class="star {half: true}" value="2" />
			<input name="rating" id="rating" type="radio" class="star {half: true}" value="3" />
			<input name="rating" id="rating" type="radio" class="star {half: true}" value="4" />
			<input name="rating" id="rating" type="radio" class="star {half: true}" value="5" />
		</div>
		<br>
		<br>
		<div class="wrapper"><a class='link1' href="javascript:rate();"><em><b>Rate this article</b></em></a></div>
		<script>
			function rate() {
				callAjax(
						"ratearticle.php", 
						{ 
							articleid: <?php echo $articleid; ?>,
							rating: $("input[name=rating]:checked").val()
						},
						function(data) {
							if (data.length > 0) {
								$("#averagerating").html(data[0].average);
								$("#yourrating").html(data[0].yourrating);
							}
						},
						false
					);
			}
		</script>
<?php

			$memberid = getLoggedOnMemberID();
			$qry = "SELECT AVG(rating) AS rating, COUNT(*) AS rows " .
					"FROM ols_articlerating " .
					"WHERE articleid = $articleid";		
			$result = mysql_query($qry);
			
			if ($result) {
				while (($member = mysql_fetch_assoc($result))) {
					echo "<h4 id='averagerating'>Average rating : (" . number_format($member['rating'], 2) . " / 5.00) from " . $member['rows'] . "". " votes</h4>";
				}
			}
			
			$qry = "SELECT * " .
					"FROM ols_articlerating " .
					"WHERE articleid = $articleid " .
					"AND memberid = $memberid";
			$result = mysql_query($qry);
			$found = false;
			
			//Check whether the query was successful or not
			
			if ($result) {
				while (($member = mysql_fetch_assoc($result))) {
?>
					<script>
						$(document).ready(
								function() {
									$("#rating").rating("select", "<?php echo $member['rating']; ?>");
								}
							);
					</script>
<?php
				}
			}
		}
	}
	
	$qry = "UPDATE ols_article " .
			"SET viewcount = viewcount + 1 " .
			"WHERE id = $articleid";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}

	include("system-footer.php"); 
?>