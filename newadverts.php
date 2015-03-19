<?php
	include("system-header.php"); 
	require_once("confirmdialog.php");
	
	createConfirmDialog("confirmrejectdialog", "Advert rejection ?", "rejectAdvert");
	
	$fromrow = 0;
	$torow = 20;
	
	if (isset($_GET['from'])) {
		$fromrow = $_GET['from'];
	}
	
	if (isset($_GET['to'])) {
		$torow = $_GET['to'];
	}
	
	$pagesize = ($torow - $fromrow);
	
	function reject() {
		$advertid = $_POST['pk1'];
		$notes = mysql_escape_string($_POST['pk2']);
		
		$qry = "UPDATE ols_advert SET " .
				"reasonforcancellation = '$notes', " .
				"published = 'X', " .
				"cancelleddate = NOW() " .
				"WHERE id = $advertid";
		$result = mysql_query($qry); 
		
		if (! $result) {
			die ($qry . " = " . mysql_error());
		}
	}
?>
<script>
	var advertID = 0;
	
	function cancelAdvert(id) {
		advertID = id;
		
		$("#rejectdialog").dialog("open");
	}
	
	function rejectAdvert() {
		call("reject", { pk1: advertID, pk2: $("#notes").val() });
	}
	$(document).ready(function() {
			$("#confirmrejectdialog .confirmdialogbody").html("You are about to reject this advert.<br>Are you sure ?");
			
			$("#rejectdialog").dialog({
					modal: true,
					autoOpen: false,
					width: 800,
					show:"fade",
					hide:"fade",
					title: "Rejection Notes",
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
		});
</script>
<div class="modal" id="rejectdialog">
	<h2>Advert Rejection</h2>
	<textarea id="notes" name="notes" cols=152 rows=10></textarea>
</div>
<div class='articles'>
	<table cellspacing=0 cellpadding=0 width='100%' class='grid list' id="articletable">
	    <thead>
	      <tr>
	        <td width='80px'>Date</td>
	        <td width='120px'>Author</td>
	        <td>Title</td>
	        <td width='80px'>Show From</td>
	        <td width='80px'>Show To</td>
	        <td width='20px'>&nbsp;</td>
	        <td width='20px'>&nbsp;</td>
	        <td width='20px'>&nbsp;</td>
	      </tr>
	    </thead>
	    <tbody>
	    	<?php
	    		$row = 1;
	    		$nextpage = false;
	    		$prevpage = ($fromrow > 0);
	    		$endrow = $torow + 1;
	    		$qry = "SELECT A.id, A.title, C.name, DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, B.login, " .
	    				"DATE_FORMAT(A.publisheddate, '%d/%m/%Y') AS publisheddate, " .
	    				"DATE_FORMAT(A.expirydate, '%d/%m/%Y') AS expirydate " .
	    				"FROM ols_advert A " .
	    				"INNER JOIN ols_members B " .
	    				"ON B.member_id = A.memberid " .
	    				"INNER JOIN ols_advertgroup C " .
	    				"ON C.id = A.groupid " .
	    				"WHERE A.published = 'N' " .
	    				"ORDER BY A.createddate ASC " .
	    				"LIMIT $fromrow, $endrow";
				$result = mysql_query($qry);
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						if ($row++ > ($pagesize)) {
							$nextpage = true;
							break;
						}
						
						echo "<tr><td width='80px'>" . $member['createddate'] . "</td>";
						echo "<td width='120px'>" . $member['login'] . "</td>";
						echo "<td>" . $member['title'] . "</td>";
						echo "<td width='80px'>" . $member['publisheddate'] . "</td>";
						echo "<td width='80px'>" . $member['expirydate'] . "</td>";
						echo "<td><a href='viewunpublishedadvert.php?id=" . $member['id'] . "'><img title='View advert' src='images/view.png' /></a></td>";
						echo "<td><a href='publishadvert.php?id=" . $member['id'] . "'><img title='Publish advert' src='images/publish.png' /></a></td>";
						echo "<td><a href='javascript: cancelAdvert(" . $member['id'] . ")'><img title='Cancel advert' src='images/cancel.png' /></a></td></tr>";
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