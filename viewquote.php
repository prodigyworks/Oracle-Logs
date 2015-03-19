<?php
	include("quotationitem.php"); 
	include("system-header.php"); 
	include("confirmdialog.php");
	
	function save() {
		$header = new QuotationHeader();
		$header->load($_GET['id']);
		$header->notes = $_POST['pk1'];
		$header->saveNotes();
	}
	
	$header = new QuotationHeader();
	$header->load($_GET['id']);
	
	createDocumentLink();
	createConfirmDialog("confirmdialog", "Save notes ?", "saveNotes");
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
		<button class="commandButton" onclick="window.open('jobreport.php?id=<?php echo $_GET['id']; ?>');">VIEW PDF</button>
		<button class="navButton" onclick="viewDocument(<?php echo $header->headerid; ?>)">DOCUMENTS</button>
		
		<?php 
			if ((isUserInRole("ADMIN") || $header->createdby == $_SESSION['SESS_MEMBER_ID']) && ($header->status != "X" && $header->status != "P")) {
				echo '<button class="actionButton" onclick="window.location.href = \'editquote.php?id=' . $_GET['id'] . '\';">EDIT</button>';
			}
		?>
	</div>
	<?php
		$header->showHeaderDetails();
		$header->showItemDetails();
    ?>
</div>
<script>
		function saveNotes() {
			call("save", { pk1: $("#completionnotes").val() });
		}
		
		$(document).ready(function() {
				<?php
					if ($header->status == "N" || $header->status == "R") {
				?>
						$("#confirmdialog .confirmdialogbody").html("You are about to save the notes for the quote <?php echo $header->getHeaderID(); ?>.<br>Are you sure ?");
				<?php
						echo "$('#apptitle').html('View Quote');\n";
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