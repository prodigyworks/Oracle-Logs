<?php
	require_once("system-db.php"); 
	require_once("confirmdialog.php"); 
	
	start_db();
	initialise_db();
	
	function removeAdvert() {
		$id = $_POST['pk1'];
		$reason = $_POST['pk2'];
		$advertid = $_GET['id'];
		
		$qry = "SELECT A.title, A.memberid " .
				"FROM ols_advert A " .
				"WHERE A.id = $id";
		$result = mysql_query($qry);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				sendUserMessage($member['memberid'], "Advert removal", "<h3>The advert has been removed</h3><h4>Reason: " . $reason . "</h3><p>" . $member['title'] . "</p>");
			}
			
		} else {
			logError($qry . " = " . mysql_error());
		}
		
		$qry = "UPDATE ols_advert " .
				"SET published = 'X', " .
				"reasonforcancellation = '" . mysql_escape_string($reason) .  "' " .
				"WHERE id = $id";
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . " = " . mysql_error());
		}
		
		header("location: allads.php");
	}
	
	function reopenAdvert() {
		$id = $_POST['pk1'];
		$reason = $_POST['pk2'];
		$advertid = $_GET['id'];
		
		$qry = "SELECT A.title, A.memberid " .
				"FROM ols_advert A " .
				"WHERE A.id = $id";
		$result = mysql_query($qry);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				sendUserMessage($member['memberid'], "Advert reopen", "<h3>The advert has been reopened</h3><h4>Reason: " . $reason . "</h3><p>" . $member['title'] . "</p>");
			}
			
		} else {
			logError($qry . " = " . mysql_error());
		}
		
		$qry = "UPDATE ols_advert " .
				"SET published = 'Y', " .
				"reasonforcancellation = '" . mysql_escape_string($reason) .  "' " .
				"WHERE id = $id";
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . " = " . mysql_error());
		}
		
		header("location: ads.php");
	}
	
	$advertid = $_GET['id'];
	$qry = "SELECT DISTINCT A.memberid, A.imageid, A.roleid, A.id, A.published, A.reasonforcancellation, A.groupid, A.title, A.url, " .
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
	$advertresult = mysql_query($qry);
	
	if ($advertresult) {
		while (($advertmember = mysql_fetch_assoc($advertresult))) {
			if (! isUserInRole("ADMIN") && $advertmember['memberid'] != getLoggedOnMemberID()) {
				header("location: system-access-denied.php");
				exit();
			}
			
			require_once("system-header.php"); 
	
			createConfirmDialog("confirmrejectdialog", "Advert rejection ?", "removeAdvert");
			createConfirmDialog("confirmreopendialog", "Advert re-open ?", "reopenAdvert");
			
			echo "<h2>Title: " . $advertmember['title'] . "</h2>";
			?>
		<form class="entryform" id="advertform" method="post" action="advertpersist.php">
			<label>Status</label>
			<input type="text" id="status" readonly name="status" value="<?php 
	if ($advertmember['published'] == "Y") {
		echo "Published";
		
	}  else if ($advertmember['published'] == "N") {
		echo "Pending";
		
	}  else if ($advertmember['published'] == "X") {
		echo "Cancelled";
	} 
	
	?>" />
		<?php
		if ($advertmember['published'] == "X") {
		?>
		<label>Reason</label>
		<textarea id="reason" cols=110 rows=5 readonly name="reason"><?php echo $advertmember['reasonforcancellation']; ?></textarea>
		<?php
		}
		?>
			
			<label>Author</label>
			<input type="text" id="author" readonly name="author" value="<?php echo $advertmember['login']; ?>" />
			
			<label>URL</label>
			<input type="text" id="url" name="url" class="textbox90" value="<?php echo $advertmember['url']; ?>" />
			
			<label>Posted on</label>
			<input type="text" readonly id="createddate" name="createddate" class="textbox10"  value="<?php echo $advertmember['createddate']; ?>" />
			
			<label>Show From</label>
			<input type="text" id="publisheddate" name="publisheddate" class="datepicker" value="<?php echo $advertmember['publisheddate']; ?>" />
			
			<label>Show To</label>
			<input type="text" id="expirydate" name="expirydate" class="datepicker" value="<?php echo $advertmember['expirydate']; ?>" />
			
			<?php
			echo "<p><br><label>Image</label><br>";
			
			if ($advertmember['groupid'] == 1) {
				echo "<img width=700 height=90 src='system-imageviewer.php?id=" . $advertmember['imageid'] . "' />";
				
			} else if ($advertmember['groupid'] == 2) {
				echo "<img width=167 height=400 src='system-imageviewer.php?id=" . $advertmember['imageid'] . "' />";
				
			} else if ($advertmember['groupid'] == 3) {
				echo "<img width=700 height=90 src='system-imageviewer.php?id=" . $advertmember['imageid'] . "' />";
				
			} else if ($advertmember['groupid'] == 4) {
				echo "<img width=700 height=100 src='system-imageviewer.php?id=" . $advertmember['imageid'] . "' />";
			}
			
			?>
			</p>
			<input type="hidden" id="advertid" name="advertid" value="<?php echo $_GET['id']; ?>" />
			<span class="wrapper"><a class='link1 rgap5' href="javascript:if (verify()) $('#advertform').submit();"><em><b>Update</b></em></a></span>
			<?php
			if (isUserInRole("ADMIN")) {
				if ($advertmember['published'] != 'X') {
			?>
			<span class="wrapper"><a class='link1 rgap5' href="javascript:remove();"><em><b>Remove</b></em></a></span>
			<?php
				} else {
			?>
			<span class="wrapper"><a class='link1 rgap5' href="javascript:reopen();"><em><b>Re-open</b></em></a></span>
			<?php
				}
			}
			?>
			
		</form>
		<div class="modal" id="rejectdialog">
			<h2>Reason for removal</h2>
			<textarea id="notes" name="notes" cols=152 rows=10></textarea>
		</div>
		<div class="modal" id="reopendialog">
			<h2>Reason for reopening</h2>
			<textarea id="reopennotes" name="reopennotes" cols=152 rows=10></textarea>
		</div>
		<script>
			$(document).ready(function() {
					$("#confirmreopendialog .confirmdialogbody").html("You are about to reopen this advert.<br>Are you sure ?");
					$("#confirmrejectdialog .confirmdialogbody").html("You are about to remove this advert.<br>Are you sure ?");
					
					$("#rejectdialog").dialog({
							modal: true,
							autoOpen: false,
							width: 800,
							show:"fade",
							hide:"fade",
							open: function(event, ui){
								$("#notes").focus();
							},
							buttons: {
								Ok: function() {
									$("#confirmrejectdialog").dialog("open");
								},
								Cancel: function() {
									$(this).dialog("close");
								}
							}
						});
					
					$("#reopendialog").dialog({
							modal: true,
							autoOpen: false,
							width: 800,
							show:"fade",
							hide:"fade",
							open: function(event, ui){
								$("#reopennotes").focus();
							},
							buttons: {
								Ok: function() {
									$("#confirmreopendialog").dialog("open");
								},
								Cancel: function() {
									$(this).dialog("close");
								}
							}
						});
				});
				
			function verify() {
				return true;
			}
			
			var currentAdvert = null;
			
			function reopen(id) {
				currentAdvert = id;
				
				$("#reopendialog").dialog("open");
			}
			
			function reopenAdvert() {
				call("reopenAdvert", {pk1: <?php echo $_GET['id']; ?>, pk2: $("#reopennotes").val()});
			}
			
			function remove(id) {
				currentAdvert = id;
				
				$("#rejectdialog").dialog("open");
				$("#rejectdialog").dialog("open");
			}
			
			function removeAdvert() {
				call("removeAdvert", {pk1: <?php echo $_GET['id']; ?>, pk2: $("#notes").val()});
			}
		</script>
			<?php
		}
		
	} else {
		logError($qry . " = " . mysql_error());
	}

	include("system-footer.php"); 
?>