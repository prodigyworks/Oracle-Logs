<?php
	include("quotationitem.php"); 
	include("system-header.php"); 
	include("confirmdialog.php");
?>
	
<script src='js/jquery.ui.timepicker.js' type='text/javascript'></script>

<?php
	
	function schedule() {
		$header = new QuotationHeader();
		$header->load($_GET['id']);
		$header->notes = $_POST['pk1'];
		$header->scheduledate = $_POST['pk2'];
		$header->schedule();
	}
	
	$header = new QuotationHeader();
	$header->load($_GET['id']);
	
	createDocumentLink();
	createConfirmDialog("confirmdialog", "Job completion ?", "scheduleJob");
?>
<div>
	<div class="modal" id="approvaldialog">
		<label>VERIFICATION NOTES</label>
		<textarea id="notes" name="notes" readonly cols=180 rows=8><?php echo $header->oldnotes; ?></textarea>
		<br>
		<br>
		<label>SCHEDULE NOTES</label>
		<textarea id="schedulenotes" name="schedulenotes" cols="180" rows=7></textarea>
		<br />
		<br />
		
		<label>Schedule Date</label>
		<input type="text" id="scheduledate" name="scheduledate" class="datepicker" value="<?php echo date("d/m/Y"); ?>" />
		<input type="text" id="scheduletime" name="scheduletime" class="timepicker" value="<?php echo date("H:i"); ?>" />
	</div>

	<?php	
		
		if ($header->status == "S") {
	?>
			<h2>Job <a title="View Job" href="viewquote?id=<?php echo $header->headerid; ?>"><?php echo $header->getHeaderID(); ?></a> has been scheduled.</h2>
			<h3>Notifications have been sent regarding changes to this job.</h3>
			<a class='backicon' href='index.php' title="Dashboard"></a>
	
	<?php
			
		} else if ($header->status == "A") {
	?>
<div class="buttonContainer">
	<button class="dataButton" onclick='$("#approvaldialog").dialog("open");'>SCHEDULE</button>
	<button class="navButton" onclick="window.open('jobreport.php?id=<?php echo $_GET['id']; ?>');">VIEW PDF</button>
	<button class="navButton" onclick="viewDocument(<?php echo $header->headerid; ?>)">DOCUMENTS</button>
</div>
	<?php
			echo "<h2>Schedule required for the following job</h2>";
			
			$header->showHeaderDetails();
			$header->showItemDetails();
		}
    ?>
</div>
<script>
	function scheduleJob() {
		call(
				"schedule", 
				{ 
					pk1: $("#schedulenotes").val(), 
					pk2: $("#scheduledate").val() + " " + $("#scheduletime").val() 
				}
			);
	}
		
	$(document).ready(function() {
			
			$("#approvaldialog").dialog({
					modal: true,
					autoOpen: false,
					width: 800,
					show:"fade",
					hide:"fade",
					title: "Schedule Job",
					open: function(event, ui){
						$("#schedulenotes").focus();
					},
					buttons: {
						Ok: function() {
							if (! isDate($("#scheduledate").val())) {
								dtAlert("Schedule date must be specified");
								$("#scheduledate").focus();
								return;
							}

							if (! isTime($("#scheduletime").val())) {
								dtAlert("Schedule time must be specified");
								$("#scheduletime").focus();
								return;
							}
								
							$("#confirmdialog .confirmdialogbody").html("You are about to schedule the job <?php echo $header->getHeaderID(); ?> for " + $("#scheduledate").val() + " " + $("#scheduletime").val() + ".<br>Are you sure ?");
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