<?php
	$isclosed = false;
	
	require_once("system-header.php"); 
	require_once("tinymce.php");

	function getParentTechnology($id, $path) {
		$qry = "SELECT A.parentid, A.name " .
				"FROM ols_technology A " .
				"WHERE A.id = $id";
		$result = mysql_query($qry);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				if ($path != "") {
					$path = $path . " / " . $member['name'];
					
				} else {
					$path = $member['name'];
				}
				
				if ($member['parentid'] != null) {
					$path = getParentTechnology($member['parentid'], $path);
				}
			}
			
		} else {
			logError($qry . " = " . mysql_error());
		}
		
		return $path;
	}
	
	function closerequest() {
		$servicerequestid = $_POST['pk1'];
		$notes = mysql_escape_string($_POST['pk2']);
		$memberid = getLoggedOnMemberID();

		$qry = "UPDATE ols_servicerequest " .
			   "SET " .
			   "status = 'C', " .
			   "closurenotes = '$notes' " .
			   "WHERE id = $servicerequestid";
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . " = " . mysql_error());
		}
		
		$qry = "INSERT INTO ols_servicerequestanswers " .
				"(servicerequestid, memberid, createddate, body, published) " .
				"VALUES " .
				"($servicerequestid, $memberid, NOW(), '<h3>Closure notes</h3><p>$notes</p>', 'N')";
				
		$result = mysql_query($qry);
		
		if (! $result) {
			logError($qry . " = " . mysql_error());
		}
		
		$qry = "SELECT A.memberid, B.login " .
				"FROM ols_servicerequest A " .
				"INNER JOIN ols_members B " .
				"ON B.member_id = A.memberid " .
				"WHERE A.id = $servicerequestid";
		$result = mysql_query($qry);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				sendUserMessage( $member['memberid'], "Service request closed", "Service request has been closed for " . $member['login'] . ".<br>Reason<br>" . $_POST['pk2']);
			}
			
		} else {
			logError($qry . " = " . mysql_error());
		}
		
		sendRoleMessage("ADMIN", "Service request closed", "Service request has been closed for " . $member['login'] . ".<br>Reason<br>" . $_POST['pk2']);
		
		$qry = "SELECT DISTINCT A.memberid, C.login " .
				"FROM ols_servicerequestanswers A " .
				"INNER JOIN ols_members C " .
				"ON C.member_id = A.memberid " .
				"WHERE A.servicerequestid = $servicerequestid";
		$result = mysql_query($qry);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				sendUserMessage( $member['memberid'], "Service request closed", "Service request has been closed for " . $member['login'] . ".<br>Reason<br>" . $_POST['pk2']);
			}
			
		} else {
			logError($qry . " = " . mysql_error());
		}
		
		sendRoleMessage("ADMIN", "Service request closed", "Service request has been closed for " . $member['login'] . ".<br>Reason<br>" . $_POST['pk2']);
		
		$isclosed = true;
	}
	
	if ($isclosed) {
		echo "<h3>Service request has been closed.</h3>";
		echo "<label>Reason for closure</label>";
		echo "<textarea cols=100 rows=5 readonly>" . $_POST['pk2'] .  "</textarea>";
	}
	
	$servicerequestid = $_GET['id'];
	$published = "N";
	$publishedrole = "";
	$memberid = null;
	$status = null;
	$consultantid = null;
	
	$qry = "SELECT A.id, A.title, A.body, A.status, A.technologyid, A.architecture, A.publishedrole, A.consultantid, A.memberid, A.published, " .
			"DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, " .
			"B.login, " .
			"C.name AS techname, " .
			"D.name AS techversion, " .
			"E.name AS opname, " .
			"F.name AS opversion " .
			"FROM ols_servicerequest A " .
			"INNER JOIN ols_members B " .
			"ON B.member_id = A.memberid " .
			"INNER JOIN ols_technology C " .
			"ON C.id = A.technologyid " .
			"INNER JOIN ols_technologyversion D " .
			"ON D.id = A.technologyversionid " .
			"INNER JOIN ols_operatingsystem E " .
			"ON E.id = A.opsystemid " .
			"INNER JOIN ols_operatingsystemversion F " .
			"ON F.id = A.opsystemversionid " .
			"WHERE A.id = $servicerequestid";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$published = $member['published'];
			$publishedrole = $member['publishedrole'];
			$memberid = $member['memberid'];
			$consultantid = $member['consultantid'];
			$status = $member['status'];
?>
<form method="POST" class="entryform" id='articleform' action='askservicerequestsave.php'>
	<h2>Title : <?php echo $member['title']; ?></h2>
	
	<label>Requested by</label>
	<input type='text' class="" readonly value="<?php echo $member['login']; ?>" />
	
	<label>Technology</label>
	<input type='text' class="textbox70" readonly value="<?php echo getParentTechnology($member['technologyid'], ""); ?>" />
	
	<label>Version</label>
	<input type='text' class="textbox10" readonly value="<?php echo $member['techversion']; ?>" />
	
	<label>Operating System</label>
	<input type='text' class="textbox70" readonly value="<?php echo $member['opname']; ?>" />
	
	<label>Version</label>
	<input type='text' class="textbox10" readonly value="<?php echo $member['opversion']; ?>" />
	
	<label>Architecture</label>
	<input type='text' class="textbox10" readonly value="<?php echo $member['architecture']; ?>" />
	<br>
	<br>
	<hr>
	<label>Service Request</label>
	<div class="body">
		<?php echo $member['body']; ?>
	</div>
</form>

<?php
		}
	} else {
		logError($qry . " = " . mysql_error());
	}
?>
<?php
	$qry = "SELECT B.* " .
			"FROM ols_servicerequestdocuments A " .
			"INNER JOIN ols_documents B " .
			"ON B.id = A.documentid " .
			"WHERE A.servicerequestid = $servicerequestid";
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
?>
<br>
<hr />
<br>
<h4>Answers</h4>
<br>
<?php
	$qry = "SELECT A.*, B.login " .
			"FROM ols_servicerequestanswers A " .
			"INNER JOIN ols_members B " .
			"ON B.member_id = A.memberid " .
			"WHERE A.servicerequestid = $servicerequestid " .
			"ORDER BY A.id";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			echo "<h5>Author : " . $member['login'] . "</h5>";
			echo "<h6>Posted : " . $member['createddate'] . "</h6>";
			
			echo "<div class='answer'>" . $member['body'] . "</div><br>";

			$qry = "SELECT B.* " .
					"FROM ols_servicerequestanswerdocuments A " .
					"INNER JOIN ols_documents B " .
					"ON B.id = A.documentid " .
					"WHERE A.answerid = " . $member['id'];
			$itemresult = mysql_query($qry);
			$first = true;
			
			if ($itemresult) {
				while (($itemmember = mysql_fetch_assoc($itemresult))) {
					if ($first) {
						$first = false;
						echo "<hr><p>Attached files. Click to view</p>";	
					}
					
					echo "<a target='_new' href='viewdocuments.php?id=" . $itemmember['id'] ."'>" . $itemmember['filename'] . "</a><br>";
				}
				
			} else {
				logError($qry . " = " . mysql_error());
			}
		}
		
	} else {
		logError($qry . " = " . mysql_error());
	}
	
	$currentmemberid = $_SESSION['SESS_MEMBER_ID'];
	
	if ($status == "O") {
		if ($currentmemberid == $memberid || $currentmemberid == $consultantid || isUserInRole("ADMIN")) {
?>
<br>
<hr />
<br>
<form method="post"  action='answerservicerequestpersist.php' id="answerform">
	<h4>Comment</h4>
	<input type="hidden" id="servicerequestid" name="servicerequestid" value="<?php echo $servicerequestid; ?>" />
	<textarea id="answerbody" name="answerbody" rows="15" cols="60" style="height:240px;width: 340px" class="tinyMCE"></textarea>
	
	<label>Attach File</label>
	<div id="upload_container">
	    <div id="filelist"></div>
	    <br />
	   	<span class="wrapper "><a id="pickfiles" class='link1 rgap5' href="javascript:;"><em><b>Select Files</b></em></a></span>
	   	<span class="wrapper"><a id="uploadfiles" class='link1' href="javascript:;"><em><b>Upload Files</b></em></a></span>
	   	<br>
	   	<br>
	</div>
	<hr />
	<br>
  	<span class="wrapper"><a id="submitanswer" class='link1 rgap5' href="javascript:if (verify()) $('#answerform').submit();"><em><b>Submit</b></em></a></span>
  	<span class="wrapper"><a class='link1' href="javascript: void(0)" onclick="javascript:closeRequest();"><em><b>Close Request</b></em></a></span>
</form>
<div id="closedialog">
	<label>Closure Notes</label>
	<textarea id="closenotes" name="closenotes" rows="15" cols="60" style="height:240px;width: 700px"></textarea>
</div>
<script type="text/javascript">
	function closeRequest() {
		$("#closedialog").dialog("open");
	}
	
	function verify() {
		var isValid = verifyStandardForm('#answerform');
		
		if ($("#answerbody").val() == "") {
			$("#answerbody_tbl").addClass("invalid");
			isValid = false;
			
		} else {
			$("#answerbody_tbl").removeClass("invalid");
		}
		
		return isValid;
	}
	
	$(document).ready(function() {
			$("#closedialog").dialog({
					modal: true,
					autoOpen: false,
					width: 720,
					title: "Close Request",
					show:"fade",
					hide:"fade",
					buttons: {
						Ok: function() {
							call("closerequest", {pk1: <?php echo $servicerequestid; ?>, pk2: $("#closenotes").val()});
						},
						Cancel: function() {
							$(this).dialog("close");
						}
					}
				});
		});
	
	var uploader = new plupload.Uploader({
		runtimes : 'gears,html5,flash,silverlight,browserplus',
		browse_button : 'pickfiles',
		container: 'upload_container',
		max_file_size : '10mb',
		url : 'system-documentupload.php',
		resize : {width : 320, height : 240, quality : 90},
		flash_swf_url : 'js/plupload.flash.swf',
		silverlight_xap_url : 'js/plupload.silverlight.xap',
		filters : [
			{title : "Image files", extensions : "jpg,gif,png"},
			{title : "Zip files", extensions : "zip"}
		]
	});
	
	uploader.bind('FilesAdded', function(up, files) {
		for (var i in files) {
			$('#filelist').html($('#filelist').html() + '<div id="' + files[i].id + '">' + files[i].name + ' (' + plupload.formatSize(files[i].size) + ') <b></b></div>');
		}
	});
	
	uploader.bind('UploadProgress', function(up, file) {
			$("#" + file.id + " b").html('<span>' + file.percent + "%</span>");
		});
	
	$('#uploadfiles').click(function() {
			uploader.start();
			return false;
		});
	
	uploader.init();
	
	$("#technologyid").change(
			function() {
				getJSONData(
						'findtechnologyversion.php?id=' + $("#technologyid").val(), 
						"#technologyversionid", 
						function() {
						}
					);
			}
		);
	
	$("#operatingsystemid").change(
			function() {
				getJSONData(
						'findopsystemversion.php?id=' + $("#operatingsystemid").val(), 
						"#opsystemversionid", 
						function() {
						}
					);
			}
		);
</script>
	
<?php
		}
	
	} else if ($status == "C") {
		if (isUserInRole("CONSULTANT")) {
?>
  	<span class="wrapper"><a id="submitanswer" class='link1 rgap5' href="answerservicerequest.php?id=<?php echo $servicerequestid; ?>&reopen=true"><em><b>Re-open</b></em></a></span>
<?php
		}
	}
	
	include("system-footer.php"); 
?>