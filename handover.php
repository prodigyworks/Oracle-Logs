<?php
	include("quotationitem.php"); 
	include("system-header.php"); 
	include("confirmdialog.php");
	
	function handover() {
		$header = new QuotationHeader();
		$header->load($_GET['id']);
		$header->notes = $_POST['pk1'];
		$header->archive();
	}
	
	$header = new QuotationHeader();
	$header->load($_GET['id']);
	
	createDocumentLink();
	createConfirmDialog("confirmdialog", "Job hand over ?", "handoverJob");
?>
<div>
	<div class="modal" id="approvaldialog">
		<label>QA NOTES</label>
		<textarea id="notes" name="notes" readonly cols=180 rows=10><?php echo $header->oldnotes; ?></textarea>
		<br>
		<br>
		<label>HANDOVER NOTES</label>
		<textarea id="handovernotes" name="handovernotes" cols="180" rows=7></textarea>
		<br />
		<br />
	</div>

	<?php	
		
		if ($header->status == "V") {
	?>
			<h2>Job <a title="View Job" href="viewquote?id=<?php echo $header->headerid; ?>"><?php echo $header->getHeaderID(); ?></a> has been handed over.</h2>
			<h3>Notifications have been sent regarding changes to this job. This job will no longer appear on the dashboard.</h3>
			<a class='backicon' href='index.php' title="Dashboard"></a>
	
	<?php
			
		} else if ($header->status == "Q") {
	?>
<div class="buttonContainer">
	<button style='<?php if ($header->status != "Q") echo "visibility:hidden"; ?>' class="dataButton" onclick='$("#approvaldialog").dialog("open");'>HANDOVER <span class='small'>WITH NOTES</span></button>
	<button class="dataButton" onclick='$("#confirmdialog").dialog("open");'>HANDOVER</button>
	<button class="navButton" onclick="window.open('jobreport.php?id=<?php echo $_GET['id']; ?>');">VIEW PDF</button>
	<button class="navButton" onclick="viewDocument(<?php echo $header->headerid; ?>)">DOCUMENTS</button>
</div>	
	<?php
			echo "<h2>Handover required for the following job</h2>";
			
			$header->showHeaderDetails();
			$header->showItemDetails();
		}
    ?>
</div>
<script>
		function handoverJob() {
			call("handover", { pk1: $("#handovernotes").val() });
		}
		
		$(document).ready(function() {
				$("#confirmdialog .confirmdialogbody").html("You are about to hand over the job <?php echo $header->getHeaderID(); ?>.<br>Are you sure ?");
				
				$("#approvaldialog").dialog({
						modal: true,
						autoOpen: false,
						width: 800,
						show:"fade",
						hide:"fade",
						title: "Hand over",
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