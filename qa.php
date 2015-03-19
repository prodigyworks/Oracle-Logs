<?php
	$temp = "";
	
	include("quotationitem.php"); 
	include("system-header.php"); 
	include("confirmdialog.php");
	
	function qa() {
		$header = new QuotationHeader();
		$header->load($_GET['id']);
		$header->notes = $_POST['pk1'];
		$header->passQa();
	}
	
	function reject() {
		$header = new QuotationHeader();
		$header->load($_GET['id']);
		$header->notes = $_POST['pk1'];
		$header->rejectQa();
		global $temp;
		
		$temp = "TEMP";
	}
	
	$header = new QuotationHeader();
	$header->load($_GET['id']);
	
	createDocumentLink();
	createConfirmDialog("confirmdialog", "QA approval ?", "qaApproveJob");
	createConfirmDialog("qarejectdialog", "QA rejection ?", "qaRejectJob");
?>
<div>
	<div class="modal" id="approvaldialog">
		<label>COMPLETION NOTES</label>
		<textarea id="notes" name="notes" readonly cols=180 rows=10><?php echo $header->oldnotes; ?></textarea>
		<br>
		<br>
		<label>QA NOTES</label>
		<textarea id="qanotes" name="qanotes" cols="180" rows=7></textarea>
		<br />
		<br />
	</div>

	<div class="modal" id="rejectdialog">
		<label>COMPLETION NOTES</label>
		<textarea id="notes" name="notes" readonly cols=180 rows=10><?php echo $header->oldnotes; ?></textarea>
		<br>
		<br>
		<label>QA REJECTION NOTES</label>
		<textarea id="rejectnotes" name="rejectnotes" cols="180" rows=7></textarea>
		<br />
		<br />
	</div>

	<?php	
		
		if ($temp == "TEMP") {
	?>
			<h2>Job <a title="View Job" href="viewquote?id=<?php echo $header->headerid; ?>"><?php echo $header->getHeaderID(); ?></a> has been rejected by <i>Quality Assurance</i>.</h2>
			<h3>Notifications have been sent regarding changes to this job.</h3>
			<a class='backicon' href='index.php' title="Dashboard"></a>
	
	<?php
			
		} else if ($header->status == "Q") {
	?>
			<h2>Job <a title="View Job" href="viewquote?id=<?php echo $header->headerid; ?>"><?php echo $header->getHeaderID(); ?></a> has been passed by <i>Quality Assurance</i>.</h2>
			<h3>Notifications have been sent regarding changes to this job.</h3>
			<a class='backicon' href='index.php' title="Dashboard"></a>
	
	<?php
			
		} else if ($header->status == "C") {
	?>
<div class="buttonContainer">
	<button style='<?php if ($header->status != "C") echo "visibility:hidden"; ?>' class="dataButton" onclick='$("#approvaldialog").dialog("open");'>QA <span class='small'>WITH NOTES</span></button>
	<button class="dataButton" onclick='$("#confirmdialog").dialog("open");'>QA</button>
	<button class="commandButton" onclick='$("#rejectdialog").dialog("open");'>REJECT</button>
	<button class="navButton" onclick="window.open('jobreport.php?id=<?php echo $_GET['id']; ?>');">VIEW PDF</button>
	<button class="navButton" onclick="viewDocument(<?php echo $header->headerid; ?>)">DOCUMENTS</button>
</div>
	<?php
			echo "<h2>QA required for the following job</h2>";
			
			$header->showHeaderDetails();
			$header->showItemDetails();
		}
    ?>
</div>
<script>
	function qaApproveJob() {
		call("qa", { pk1: $("#qanotes").val() });
	}
	
	function qaRejectJob() {
		call("reject", { pk1: $("#rejectnotes").val() });
	}
	
		$(document).ready(function() {
				$("#confirmdialog .confirmdialogbody").html("You are about to QA the job <?php echo $header->getHeaderID(); ?>.<br>Are you sure ?");
				$("#qarejectdialog .confirmdialogbody").html("You are about to reject the job <?php echo $header->getHeaderID(); ?>.<br>Are you sure ?");
				
				$("#approvaldialog").dialog({
						modal: true,
						autoOpen: false,
						width: 800,
						show:"fade",
						hide:"fade",
						title: "QA Job",
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

		$(document).ready(function() {
				$("#rejectdialog").dialog({
						modal: true,
						autoOpen: false,
						width: 800,
						show:"fade",
						title: "Reject Job",
						hide:"fade",
						buttons: {
							Ok: function() {
								$("#qarejectdialog").dialog("open");
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