<?php
function removeArticle() {
	$id = $_POST['pk1'];
	
	$qry = "SELECT title, memberid FROM ols_article WHERE id = $id";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$title = $member['title'];
			sendRoleMessage(
					"ADMIN",
					"Article removal",
					"<h4>Article $title has been removed</h4>"
				);
			
			sendUserMessage(
					$member['memberid'],
					"Article removal",
					"<h4>Article $title has been removed</h4>"
				);
		}
	}
	
	$qry = "DELETE FROM ols_articledocuments WHERE articleid = $id";
	$result = mysql_query($qry);

	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
	
	$qry = "DELETE FROM ols_article WHERE id = $id";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
}

function withdrawArticle() {
	$id = $_POST['pk1'];
	$reasonforcancellation = $_POST['pk2'];
	$qry = "SELECT title, memberid FROM ols_article WHERE id = $id";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			$title = $member['title'];
			sendRoleMessage(
					"ADMIN",
					"Article withdrawal",
					"<h4>Article $title has been withdrawn from publication</h4><p>Reason:</p>" . $reasonforcancellation
				);
			
			sendUserMessage(
					$member['memberid'],
					"Article withdrawal",
					"<h4>Article $title has been withdrawn from publication</h4><p>Reason:</p>" . $reasonforcancellation
				);
		}
	}
	
	$reasonforcancellation = mysql_escape_string($reasonforcancellation);
	$qry = "UPDATE ols_article SET published = 'N', cancelleddate = NOW(), reasonforcancellation = '$reasonforcancellation' WHERE id = $id";
	$result = mysql_query($qry);
	
	if (! $result) {
		logError($qry . " = " . mysql_error());
	}
}

function article($where) {
	require_once("system-header.php"); 
	require_once("confirmdialog.php"); 
	require_once("tinymce.php"); 
	
	createConfirmDialog("removedialog", "Remove article ?", "deleteArticle");
	createConfirmDialog("withdrawdialog", "Withdraw article ?", "withdrawReason");
	
	$fromrow = 0;
	$torow = 20;
	
	if (isset($_GET['from'])) {
		$fromrow = $_GET['from'];
	}
	
	if (isset($_GET['to'])) {
		$torow = $_GET['to'];
	}
	
	$pagesize = ($torow - $fromrow);
	$viewer = "viewarticle.php?callee=" . base64_encode(basename($_SERVER['PHP_SELF']));
?>
<div id="reasondialog" class="modal">
	<label>Reason</label>
	<textarea id="reason" name="reason" class="tinyMCE" style='width:770px; height: 300px'></textarea>
</div>

<div class='articles'>
	<table width=100% class='grid list' id="xx" cellspacing=0 cellpadding=0>
	    <thead>
	      <tr>
	        <td width='20px'>&nbsp;</td>
	        <td width='20px'>&nbsp;</td>
	        <td width='75px'>Date</td>
	        <td>Title</td>
	        <td>Author</td>
	        <td width='75px' align=center>Rating</td>
	        <td width='75px'>Expires</td>
	        <td width='20px'>&nbsp;</td>
	        <?php
	        	if (isUserInRole("CONSULTANT")) {
	        ?>
	        <td width='20px'>&nbsp;</td>
	        <td width='20px'>&nbsp;</td>
	        <?php
	        	}
	        ?>
	      </tr>
	    </thead>
	    <tbody>
	    	<?php
	    		$row = 1;
	    		$nextpage = false;
	    		$prevpage = ($fromrow > 0);
	    		$endrow = $torow + 1;

	    		$qry = "SELECT DISTINCT A.published, A.featured, A.id, A.title, " .
	    				"DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, " .
	    				"DATE_FORMAT(A.publisheddate, '%d/%m/%Y') AS publisheddate, " .
	    				"DATE_FORMAT(A.expirydate, '%d/%m/%Y') AS expirydate, " .
	    				"B.login, " .
	    				"(SELECT AVG(rating) FROM ols_articlerating X WHERE X.articleid = A.id) AS rating " .
	    				"FROM ols_article A " .
	    				"INNER JOIN ols_members B " .
	    				"ON B.member_id = A.memberid " .
	    				"$where " .
	    				"ORDER BY A.createddate DESC " .
	    				"LIMIT $fromrow, $endrow";
				$result = mysql_query($qry);
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						if ($row++ > ($pagesize)) {
							$nextpage = true;
							break;
						}
						
						echo "<tr>\n";
						
						if ($member['published'] == "Y") {
							echo "<td width='20px'><img src='images/published.png'  title='Published' /></td>";
						
						} else if ($member['published'] == "X") {
							echo "<td width='20px'><img src='images/cancel.png'  title='Cancelled' /></td>";
						
						} else {
							echo "<td width='20px'><img src='images/unpublished.png' title='Awaiting Publish' /></td>";
						}
						
						if ($member['featured'] == "Y") {
							echo "<td width='20px'><img src='images/hot.png'  title='Featured' /></td>";
						
						} else {
							echo "<td width='20px'>&nbsp;</td>";
						}
						
						echo "<td width='75px'>" . $member['createddate'] . "</td>";
						echo "<td><a href='$viewer&id=" . $member['id'] . "'>" . $member['title'] . "</a></td>";
						echo "<td>" . $member['login'] . "</td>";
						echo "<td align=center width='75px'>" . number_format($member['rating'], 2) . "</td>";
						
						if ($member['published'] == "Y") {
							echo "<td width='75px'>" . $member['expirydate'] . "</td>";

						} else {
							echo "<td width='75px'>&nbsp;</td>";
						}
						
						echo "<td width='20px'><a href='$viewer&id=" . $member['id'] . "'><img src='images/view.png'  title='View article' /></a></td>";
			        	
			        	if (isUserInRole("CONSULTANT")) {
							echo "<td width='20px'><a href='javascript: remove(" . $member['id'] . ")'><img src='images/delete.png'  title='Delete article and contents' /></a></td>";
							echo "<td width='20px'><a href='javascript: withdraw(" . $member['id'] . ")'><img src='images/unpublish.png'  title='Withdraw article from publication' /></a></td>";
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
<script>
	var articleid = null;
	
	$(document).ready(function() {
			$("#reasondialog").dialog({
					modal: true,
					autoOpen: false,
					title: "Reason for withdrawal",
					width: 800,
					height: 420,
					buttons: {
						Ok: function() {
							tinyMCE.triggerSave();
							
							withdrawArticle();
						},
						Cancel: function() {
							$(this).dialog("close");
						}
					}
				});
		});
		
	function withdrawReason() {
		$("#withdrawdialog").dialog("close");
		$("#reasondialog").dialog("open");
	}
	
	function withdraw(id) {
		articleid = id;
		
		$("#withdrawdialog .confirmdialogbody").html("You are about to withdraw this article from public access.<br>Are you sure ?");
		$("#withdrawdialog").dialog("open");
	}
	
	function remove(id) {
		articleid = id;
		
		$("#removedialog .confirmdialogbody").html("You are about to remove this article.<br>Are you sure ?");
		$("#removedialog").dialog("open");
	}
	
	function withdrawArticle(id) {
		call("withdrawArticle", {pk1: articleid, pk2: $("#reason").val() });
	}
	
	function deleteArticle(id) {
		call("removeArticle", {pk1: articleid});
	}
</script>
<?php
	include("system-footer.php");
} 
?>