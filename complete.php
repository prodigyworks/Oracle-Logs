<?php
	include("quotationitem.php"); 
	include("system-header.php"); 
	include("confirmdialog.php");
	
	function complete() {
		$header = new QuotationHeader();
		$header->load($_GET['id']);
		$header->notes = $_POST['pk1'];
		$header->completeQuote();
	}
	
	$header = new QuotationHeader();
	$header->load($_GET['id']);
	
	createDocumentLink();
	createConfirmDialog("confirmdialog", "Job completion ?", "completeJob");
?>
<div>
	<div class="modal" id="approvaldialog">
		<label>CE APPROVAL NOTES</label>
		<textarea id="notes" name="notes" readonly cols=180 rows=10><?php echo $header->oldnotes; ?></textarea>
		<br>
		<br>
		<label>COMPLETION NOTES</label>
		<textarea id="completionnotes" name="completionnotes" cols="180" rows=7></textarea>
		<br />
		<br />
	</div>

	<?php	
		
		if ($header->status == "C") {
	?>
			<h2>Job <a title="View Job" href="viewquote?id=<?php echo $header->headerid; ?>"><?php echo $header->getHeaderID(); ?></a> has been completed.</h2>
			<h3>Notifications have been sent regarding changes to this job.</h3>
			<a class='backicon' href='index.php' title="Dashboard"></a>
	
	<?php
			
		} else if ($header->status == "I") {
	?>
<div class="buttonContainer">
	<button style='<?php if ($header->status != "I") echo "visibility:hidden"; ?>' class="dataButton" onclick='$("#approvaldialog").dialog("open");'>COMPLETE</button>
	<button class="navButton" onclick="window.open('jobreport.php?id=<?php echo $_GET['id']; ?>');">VIEW PDF</button>
	<button class="navButton" onclick="viewDocument(<?php echo $header->headerid; ?>)">DOCUMENTS</button>
</div>
	<?php
			echo "<h2>Completion required for the following job</h2>";
			
			$header->showHeaderDetails();
			$header->showItemDetails();
		}
    ?>
</div>
<script>
		function completeJob() {
			call("complete", { pk1: $("#completionnotes").val() });
		}
		
		$(document).ready(function() {
				$("#confirmdialog .confirmdialogbody").html("You are about to complete the job <?php echo $header->getHeaderID(); ?>.<br>Are you sure ?");
				
				$("#approvaldialog").dialog({
						modal: true,
						autoOpen: false,
						width: 800,
						show:"fade",
						hide:"fade",
						title: "Complete job",
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