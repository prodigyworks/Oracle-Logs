<?php
	include("system-header.php"); 
	
	$advertid = $_GET['id'];
	$qry = "SELECT DISTINCT A.imageid, A.roleid, A.id, A.title, A.url, " .
			"DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, " .
			"DATE_FORMAT(A.publisheddate, '%d/%m/%Y') AS publisheddate, " .
			"DATE_FORMAT(A.expirydate, '%d/%m/%Y') AS expirydate, " .
			"B.login, C.name, " .
			"(SELECT COUNT(*) FROM ols_advert X WHERE X.id = A.id AND X.publisheddate <= NOW() AND X.expirydate >= NOW()) AS active  " .
			"FROM ols_advert A " .
			"INNER JOIN ols_members B " .
			"ON B.member_id = A.memberid " .
			"INNER JOIN ols_advertgroup C " .
			"ON C.id = A.groupid " .
			"WHERE A.id = $advertid";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			echo "<h2>Title: " . $member['title'] . "</h2>";
			echo "<h4>Author : " . $member['login'] . "</h4>";
			echo "<h4>URL : " . $member['url'] . "</h4>";
			echo "<h5>Posted on : " . $member['createddate'] . "</h5>";
			echo "<h5>Show From :  " . $member['publisheddate'] . "</h5>";
			echo "<h5>Show To : " . $member['expirydate'] . "</h5>";
			echo "<h5>Group : " . $member['name'] . "</h5>";
			echo "<p><br><label>Image</label><br><img src='system-imageviewer.php?id=" . $member['imageid'] . "' /></p>";
?>
	<form id="publishForm" method="POST" action="publishadvertpersist.php">
		<div id="dummypanel" style="display:none"></div>
	</form>
	
	<div id="dialog" class="modal">
		<div id="publishpanel">
			<input type="hidden" id="advertid" name="advertid" value="<?php echo $_GET['id']; ?>" />
			<table width='100%'>
				<tr>
					<td>Publish to role</td>
					<td>
						<?php createCombo("roleid", "roleid", "roleid", "ols_roles"); ?>
					</td>
				</tr>
				<tr>
					<td>Show from date</td>
					<td><input type="text" id="publishdate" name="publishdate" class="datepicker" value="<?php echo $member['publisheddate']; ?>" /></td>
				</tr>
				<tr>
					<td>Expiry date</td>
					<td><input type="text" id="expirydate" name="expirydate" class="datepicker" value="<?php echo $member['expirydate']; ?>"  /></td>
				</tr>
			</table>
		</div>
	</div>
	<br>
	<hr>
	<br>
  	<span class="wrapper"><a class='link1 rgap5' href="javascript:publish();"><em><b>Publish</b></em></a></span>
  	<span class="wrapper"><a class='link1' href="javascript:back();"><em><b>Back</b></em></a></span>
  	<script>
		$(document).ready(function() {
				$("#roleid").val("<?php echo $member['roleid']; ?>");
				$("#dialog").dialog({
						autoOpen: false,
						modal: true,
						title: "Publish",
						buttons: {
							Ok: function() {
								$(this).dialog("close");
								
								$("#publishpanel").appendTo("#dummypanel");
								$("#publishForm").submit();
							},
							Cancel: function() {
								$(this).dialog("close");
							}
						}
					});
			});
		
  		function publish() {
  			$("#dialog").dialog("open");
  		}
  		
  		function back() {
  			window.location.href = "newadverts.php";
  		}
  	</script>
<?php
		}
		
	} else {
		logError($qry . " = " . mysql_error());
	}
	include("system-footer.php"); 
?>