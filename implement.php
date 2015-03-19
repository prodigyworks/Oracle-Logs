<?php
	$temp = "";
	
	include("quotationitem.php"); 
	include("system-header.php"); 
	include("confirmdialog.php");
	
	function implement() {
		$header = new QuotationHeader();
		$header->load($_GET['id']);
		$header->notes = $_POST['pk1'];
		$header->implement();
	}
	
	function reject() {
		$header = new QuotationHeader();
		$header->load($_GET['id']);
		$header->notes = $_POST['pk1'];
		$header->implementreject();
		global $temp;
		
		$temp = "TEMP";
	}
	
	$header = new QuotationHeader();
	$header->load($_GET['id']);
	
	createDocumentLink();
	createConfirmDialog("confirmdialog", "CE approval ?", "ceApproveJob");
	createConfirmDialog("cerejectdialog", "CE rejection ?", "ceRejectJob");
	
?>
<div>
	<div class="modal" id="approvaldialog">
		<label>SCHEDULE NOTES</label>
		<textarea id="notes" name="notes" readonly cols=180 rows=10><?php echo $header->oldnotes; ?></textarea>
		<br>
		<br>
		<label>CE APPROVAL NOTES</label>
		<textarea id="ceapprovalnotes" name="ceapprovalnotes" cols="180" rows=7></textarea>
	</div>
	<div class="modal" id="rejectdialog">
		<label>SCHEDULE NOTES</label>
		<textarea id="notes" name="notes" readonly cols=180 rows=10><?php echo $header->oldnotes; ?></textarea>
		<br>
		<br>
		<label>CE REJECTION NOTES</label>
		<textarea id="cerejectnotes" name="cerejectnotes" cols="180" rows=7></textarea>
	</div>

	<?php	
		if ($temp == "TEMP") {
	?>
			<h2>Job <a title="View Job" href="viewquote?id=<?php echo $header->headerid; ?>"><?php echo $header->getHeaderID(); ?></a> has been rejected by <i>Critical Environment</i>.</h2>
			<h3>Notifications have been sent regarding changes to this job.</h3>
			<a class='backicon' href='index.php' title="Dashboard"></a>
	
	<?php
			
		} else if ($header->status == "I") {
	?>
			<h2>Job <a title="View Job" href="viewquote?id=<?php echo $header->headerid; ?>"><?php echo $header->getHeaderID(); ?></a> has been approved by <i>Critical Environment</i>.</h2>
			<h3>Notifications have been sent regarding changes to this job.</h3>
			<a class='backicon' href='index.php' title="Dashboard"></a>
	
	<?php
			
		} else if ($header->status == "S") {
	?>
<div class="buttonContainer">
	<button style='<?php if ($header->status != "S") echo "visibility:hidden"; ?>' class="dataButton" onclick='$("#approvaldialog").dialog("open");'>APPROVAL <span class='small'>WITH NOTES</span></button>
	<button class="dataButton" onclick='$("#confirmdialog").dialog("open");'>APPROVE</button>
	<button class="commandButton" onclick='$("#rejectdialog").dialog("open");'>REJECT</button>
	<button class="navButton" onclick="window.open('quotationreport.php?id=<?php echo $_GET['id']; ?>');">VIEW PDF</button>
	<button class="navButton" onclick="viewDocument(<?php echo $header->headerid; ?>)">DOCUMENTS</button>
</div>
	<?php
			echo "<h2>CE Approval required for the following job</h2>";
			
			$header->showHeaderDetails();
			$header->showItemDetails();
		}
    ?>
</div>
<script>
	function ceApproveJob() {
		call("implement", { pk1: $("#ceapprovalnotes").val() });
	}
	
	function ceRejectJob() {
		call("implementreject", { pk1: $("#cerejectnotes").val() });
	}
	
	$(document).ready(function() {
			$("#confirmdialog .confirmdialogbody").html("You are about to approve the job <?php echo $header->getHeaderID(); ?>.<br>Are you sure ?");
			$("#cerejectdialog .confirmdialogbody").html("You are about to reject the job <?php echo $header->getHeaderID(); ?>.<br>Are you sure ?");
			$("#approvaldialog").dialog({
					modal: true,
					autoOpen: false,
					width: 800,
					title: "CE Approval",
					show:"fade",
					hide:"fade",
					open: function(event, ui){
						$("#ceapprovalnotes").focus();
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
					width: 800,
					show:"fade",
					hide:"fade",
					title: "CE Rejection",
					open: function(event, ui){
						$("#cerejectnotes").focus();
					},
					buttons: {
						Ok: function() {
							$("#cerejectdialog").dialog("open");
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