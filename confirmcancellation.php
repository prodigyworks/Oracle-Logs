<?php
	$eventApproved = false;
	$eventRejected = false;
	
	include("quotationitem.php"); 
	include("system-header.php"); 
	include("confirmdialog.php");
	
	function approve() {
		global $eventApproved;
		
		$header = new QuotationHeader();
		$header->load($_GET['id']);
		$header->notes = $_POST['pk1'];
		$header->approveCancellation();
		
		$eventApproved = true;
	}
	
	function reject() {
		global $eventRejected;
		
		$header = new QuotationHeader();
		$header->load($_GET['id']);
		$header->notes = $_POST['pk1'];
		$header->rejectCancellation();
		
		$eventRejected = true;
	}
	
	$header = new QuotationHeader();
	$header->load($_GET['id']);
	
	createDocumentLink();
?>
<style>
	#displaynotes {
		position: absolute;
		top: 170px;
		left: 700px;
	}
	#displaynotes textarea {
		width: 520px;
		height: 200px;
	}
</style>
<div>
	<div class="modal" id="approvaldialog">
		<label>DEFINITION NOTES</label>
		<textarea id="notes" name="notes" readonly cols=180 rows=10><?php echo $header->oldnotes; ?></textarea>
		<br>
		<br>
		<label>CANCELLATION APPROVAL NOTES</label>
		<textarea id="approvalnotes" name="approvalnotes" cols="180" rows=7></textarea>
		<br>
		<br>
	</div>
	<div class="modal" id="rejectdialog">
		<label>DEFINITION NOTES</label>
		<textarea id="notes" name="notes" readonly cols=180 rows=10><?php echo $header->oldnotes; ?></textarea>
		<br>
		<br>
		<label>CANCELLATION REJECTION NOTES</label>
		<textarea id="rejectnotes" name="rejectnotes" cols="180" rows=7></textarea>
	</div>

	<?php	
		createConfirmDialog("confirmdialog", "Job cancellation approval ?", "approveCancel");
		createConfirmDialog("rejectcanceldialog", "Job cancellation rejection ?", "rejectCancel");
	
		if ($eventApproved) {
			if ($header->status == "X") {
	?>
			<h2>Job <a title="View Job" href="viewquote?id=<?php echo $header->headerid; ?>"><?php echo $header->getHeaderID(); ?></a> has been cancelled.</h2>
			<h3>Notifications have been sent regarding changes to this job.</h3>
			<a class='backicon' href='index.php' title="Dashboard"></a>
	
	<?php
			} else {
	?>
			<h2>Job <a title="View Job" href="viewquote?id=<?php echo $header->headerid; ?>"><?php echo $header->getHeaderID(); ?></a> has been approved for cancellation.</h2>
			<h3>Complete cancellation will commence when all users agree cancellation terms.</h3>
			<a class='backicon' href='index.php' title="Dashboard"></a>
	
	<?php
			}
		} else if ($eventRejected) {
	?>
			<h2>Job <a title="View Job" href="viewquote?id=<?php echo $header->headerid; ?>"><?php echo $header->getHeaderID(); ?></a> has been rejected for cancellation.</h2>
			<h3>This job will remain in <i>Pending</i> status until terms have been agreed.</h3>
			<a class='backicon' href='index.php' title="Dashboard"></a>
	
	<?php
		} else if ($header->status == "P") {
	?>

<div class="buttonContainer">
	<button class="dataButton" onclick='$("#approvaldialog").dialog("open");'>APPROVE</button>
	<button class="commandButton" onclick='$("#rejectdialog").dialog("open");'>REJECT</button>
	<button class="navButton" onclick="window.open('jobreport.php?id=<?php echo $_GET['id']; ?>');">VIEW PDF</button>
	<button class="navButton" onclick="viewDocument(<?php echo $header->headerid; ?>)">DOCUMENTS</button>
</div>
	<?php			
			echo "<h2>Approval required for the cancellation of the following quotation</h2>";
			
			$header->showHeaderDetails();
			$header->showItemDetails();
    ?>
    <div id="displaynotes">
	    <label>Reason for cancellation</label>
		<textarea readonly><?php echo $header->notes; ?></textarea>
	</div>
	<?php			
			
		}
    ?>
</div>
<script>
	function approveCancel() {
		call("approve", { pk1: $("#approvalnotes").val(), pk2: $("#internalbudget").val() });
	}
	
	function rejectCancel() {
		call("reject", { pk1: $("#rejectnotes").val() });
	}
	
	$(document).ready(function() {
			$("#confirmdialog .confirmdialogbody").html("You are about to approve the cancellation of quotation <?php echo $header->getHeaderID(); ?>.<br>Are you sure ?");
			$("#rejectcanceldialog .confirmdialogbody").html("You are about to reject the cancellation of quotation <?php echo $header->getHeaderID(); ?>.<br>Are you sure ?");
		
			$("#approvaldialog").dialog({
					modal: true,
					autoOpen: false,
					width: 800,
					show:"fade",
					hide:"fade",
					title: "Approve cancellation",
					open: function(event, ui){
						$("#approvalnotes").focus();
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
				
			$("#rejectdialog").dialog({
					modal: true,
					autoOpen: false,
					title: "Reject cancellation",
					width: 800,
					show:"fade",
					hide:"fade",
					open: function(event, ui){
						$("#rejectnotes").focus();
					},
					buttons: {
						Ok: function() {
							$("#rejectcanceldialog").dialog("open");
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