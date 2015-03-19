<?php
	include("quotationitem.php"); 
	include("system-header.php"); 
	include("confirmdialog.php");
	
	function save() {
		$header = new QuotationHeader();
		$header->loadThrowAway($_GET['id']);
		$header->notes = $_POST['pk1'];
		$header->saveThrowAwayNotes();
	}
	
	$header = new QuotationHeader();
	$header->loadThrowAway($_GET['id']);
	
	createDocumentLink();
	createConfirmDialog("confirmdialog", "Save notes ?", "saveNotes");
	createConfirmDialog("removedialog", "Remove Throw Away Quote ?", "removeThrowAwayQuote");
	
	function removeQuote() {
		$header = new QuotationHeader();
		$header->loadThrowAway($_POST['pk1']);
		$header->deleteThrowAwayQuote();
	}
	
	if ($header->headerid != 0) {
?>
<div>
	<div class="modal" id="approvaldialog">
		<label>JOB NOTES</label>
		<textarea id="notes" name="notes" readonly cols=180 rows=10><?php echo $header->oldnotes; ?></textarea>
		<br>
		<br>
		<label>ADDITIONAL NOTES</label>
		<textarea id="completionnotes" name="completionnotes" cols="180" rows=7></textarea>
		<br />
		<br />
	</div>

	<div class="buttonContainer">
		<button class="dataButton" onclick='$("#approvaldialog").dialog("open");'>NOTES</button>
		<button class="navButton" onclick="viewDocument(<?php echo $header->headerid; ?>)">DOCUMENTS</button>
		
		<?php 
			if ((isUserInRole("ADMIN") || $header->createdby == $_SESSION['SESS_MEMBER_ID']) && ($header->status != "X" && $header->status != "P")) {
				echo '<button class="commandButton" onclick="window.location.href = \'editthrowawayquote.php?id=' . $_GET['id'] . '\';">EDIT</button>';
				echo '<button class="commandButton" onclick="window.location.href = \'editthrowawayquoteasnew.php?id=' . $_GET['id'] . '\';">CONVERT<span class=\'small\'>TO LIVE QUOTE</span></button>';
				echo '<button class="actionButton" onclick="removeQuote()">DELETE</button>';
			}
		?>
	</div>
	<?php
		$header->showHeaderDetails();
		$header->showItemDetails();
		
	} else {
?>
<h2>Throw Away Quotation has been removed.</h2>

<a class='backicon' href='index.php' title="Dashboard"></a>
	<?php
	}
    ?>
</div>
<script>
	function removeQuote() {
		$("#removedialog .confirmdialogbody").html("You are about to remove the throw away quote <?php echo $header->getHeaderID(); ?>.<br>Are you sure ?");
		$("#removedialog").dialog("open");
	}
	
	function removeThrowAwayQuote() {
		call("removeQuote", {pk1: <?php echo $header->headerid; ?>});
	}
		
		function saveNotes() {
			call("save", { pk1: $("#completionnotes").val() });
		}
		
		$(document).ready(function() {
				<?php
					if ($header->status == "N" || $header->status == "R") {
				?>
						$("#confirmdialog .confirmdialogbody").html("You are about to save the notes for the throw away quote <?php echo $header->getHeaderID(); ?>.<br>Are you sure ?");
				<?php
						echo "$('#apptitle').html('View Throw Away Quote');\n";
					} else {
				?>
						$("#confirmdialog .confirmdialogbody").html("You are about to save the notes for the job <?php echo $header->getHeaderID(); ?>.<br>Are you sure ?");
				<?php
					}
				?>
				
				$("#approvaldialog").dialog({
						modal: true,
						autoOpen: false,
						width: 800,
						show:"fade",
						hide:"fade",
						title: "Notes",
						open: function(event, ui){
							$("#completionnotes").focus();
						},
						buttons: {
							Ok: function() {
								$("#confirmdialog").dialog("open");
							},
							Cancel: function() {
								$(this).dialog("close");
							}
						}
					});
			});

</script>

<?php

	include("system-footer.php"); 
?>