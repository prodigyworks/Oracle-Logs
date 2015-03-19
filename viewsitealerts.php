

<?php
	include("system-header.php"); 
	
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
	<table width=100% class='grid list' id="xx" cellspacing=0 cellpadding=0>
	    <thead>
	      <tr>
	        <td width='80px'>Date</td>
	        <td>Title</td>
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
	    		$qry = "SELECT A.id, A.title, A.body, " .
	    				"DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate " .
						"FROM ols_sitealert A " . 
	    				"ORDER BY A.id DESC " .
	    				"LIMIT $fromrow, $endrow";
				$result = mysql_query($qry);
				
				if ($result) {
					while (($member = mysql_fetch_assoc($result))) {
						if ($row++ > ($pagesize)) {
							$nextpage = true;
							break;
						}
						
						echo "<tr>\n";
						
						echo "<td>" . $member['createddate'] . "</td>";
						echo "<td><a href='viewsitealert.php?callee=" . base64_encode(basename($_SERVER['PHP_SELF'])) . "&id=" . $member['id'] . "'>" . $member['title'] . "</a></td>";
						echo "<td width='20px'><div id='prev' style='display:none'>" . $member['body'] . "</div><img  onclick='viewbody(this)' src='images/view.png'  title='View email alert' /></td>";
						echo "<td width='20px'><a href='viewsitealert.php?callee=" . base64_encode(basename($_SERVER['PHP_SELF'])) . "&id=" . $member['id'] . "'><img src='images/user.png'  title='View members' /></a></td>";
						echo "<td width='20px'><a href='resendsitealert.php?callee=" . base64_encode(basename($_SERVER['PHP_SELF'])) . "&id=" . $member['id'] . "'><img src='images/recycle.png'  title='Resend' /></a></td>";
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
<div class="modal" id="alertpreviewdialog">
	<span id="alertpreview">TESTER</span>
</div>
<script>
	$(document).ready(function() {
			$("#alertpreviewdialog").dialog({
					modal: true,
					autoOpen: false,
					width: 800,
					title: "alertpreview",
					show:"fade",
					hide:"fade",
					buttons: {
						Ok: function() {
							$(this).dialog("close");
						}
					}
				});
		});
		
	function viewbody(node) {
		$("#alertpreview").html($(node).prev().html());
		$("#alertpreviewdialog").dialog("open");
	}
</script>
<?php
	include("system-footer.php"); 
?>