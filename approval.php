<?php
	include("quotationitem.php"); 
	include("system-header.php"); 
	include("confirmdialog.php");
		
	function approve() {
		$header = new QuotationHeader();
		$header->load($_GET['id']);
		$header->notes = $_POST['pk1'];
		$header->internalapprovalcode = $_POST['pk2'];
		$header->approve();
	}
	
	function reject() {
		$header = new QuotationHeader();
		$header->load($_GET['id']);
		$header->notes = $_POST['pk1'];
		$header->reject();
	}
	
	$header = new QuotationHeader();
	$header->load($_GET['id']);
	
	createDocumentLink();
	createConfirmDialog("confirmapprovaldialog", "Quotation approval ?", "approveQuote");
	createConfirmDialog("confirmrejectdialog", "Quotation rejection ?", "rejectQuote");
?>
<div>
	<div class="modal" id="approvaldialog">
		<label>DEFINITION NOTES</label>
		<textarea id="notes" name="notes" readonly cols=180 rows=10><?php echo $header->oldnotes; ?></textarea>
		<br>
		<br>
		<label>VERIFICATION NOTES</label>
		<textarea id="approvalnotes" name="approvalnotes" cols="180" rows=10></textarea>
		<br>
		<br>
		
		<?php
			if ($header->costcode == "OPEXINTERNAL" || $header->costcode == "CAPEXINTERNAL") {
		?>
		<label>INTERNAL BUDGET REFERENCE</label>
		<?php
			}
		?>
		<input type="<?php if ($header->costcode == "OPEXINTERNAL" || $header->costcode == "CAPEXINTERNAL") echo "text"; else echo "hidden";?>" id="internalbudget" name="internalbudget" style='width:250px' />
	</div>
	<div class="modal" id="rejectdialog">
		<label>DEFINITION NOTES</label>
		<textarea id="notes" name="notes" readonly cols=180 rows=10><?php echo $header->oldnotes; ?></textarea>
		<br>
		<br>
		<label>REASON FOR REJECTION NOTES</label>
		<textarea id="rejectnotes" name="rejectnotes" cols="180" rows=7></textarea>
	</div>

	<?php	
		if ($header->status == "A") {
	?>
			<h2>Job <a title="View Job" href="viewquote?id=<?php echo $header->headerid; ?>"><?php echo $header->getHeaderID(); ?></a> has been approved.</h2>
			<h3>Notifications have been sent regarding changes to this job.</h3>
			<a class='backicon' href='index.php' title="Dashboard"></a>
	
	<?php
		} else if (($header->status == "N" || $header->status == "R") && $header->approvalid == null) {
	?>
			<h2>Job <a title="View Job" href="viewquote?id=<?php echo $header->headerid; ?>"><?php echo $header->prefix . sprintf("%04d", $header->headerid); ?></a> has been rejected.</h2>
			<h3>Notifications have been sent regarding changes to this job.</h3>
			<a class='backicon' href='index.php' title="Dashboard"></a>
	<?php
		} else if ($header->status == "N" || $header->status == "R") {
	?>


<div class="buttonContainer">
	<button class="dataButton" onclick='$("#approvaldialog").dialog("open");'>VERIFY</button>
	<button class="commandButton" onclick='$("#rejectdialog").dialog("open");'>REJECT</button>
	<button class="navButton" onclick="window.open('quotationreport.php?id=<?php echo $_GET['id']; ?>');">VIEW PDF</button>
	<button class="navButton" onclick="viewDocument(<?php echo $header->headerid; ?>)">DOCUMENTS</button>
</div>
	<?php			
			echo "<h2>Verification required for the following quotation</h2>";
			
			$header->showHeaderDetails();
			$header->showItemDetails();
		}
    ?>
</div>
<script>
	function approveQuote() {
		call("approve", { pk1: $("#approvalnotes").val(), pk2: $("#internalbudget").val() });
	}
	
	function rejectQuote() {
		call("reject", { pk1: $("#rejectnotes").val() });
	}
	
	$(document).ready(function() {
			$("#confirmapprovaldialog .confirmdialogbody").html("You are about to approve the quotation <?php echo $header->getHeaderID(); ?>.<br>Are you sure ?");
			$("#confirmrejectdialog .confirmdialogbody").html("You are about to reject the quotation <?php echo $header->getHeaderID(); ?>.<br>Are you sure ?");
			
			$("#approvaldialog").dialog({
					modal: true,
					autoOpen: false,
					width: 800,
					show:"fade",
					hide:"fade",
					title: "Approval Notes",
					open: function(event, ui){
						$("#approvalnotes").focus();
					},
					buttons: {
						Ok: function() {
							$("#confirmapprovaldialog").dialog("open");
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
					open: function(event, ui){
						$("#rejectnotes").focus();
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

<?php

	include("system-footer.php"); 
?>