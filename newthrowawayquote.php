<?php 
	require_once("quotationitem.php");
	include("system-header.php"); 
	
	if (! isset($_SESSION['QUOTATION'])) {
		$_SESSION['QUOTATION'] = new QuotationHeader();
		$_SESSION['QUOTATION']->createdby = $_SESSION['SESS_MEMBER_ID'];
		$_SESSION['QUOTATION']->contactid = $_SESSION['SESS_MEMBER_ID'];
		$_SESSION['QUOTATION']->status = "N";
	}
	
	$header = $_SESSION['QUOTATION'];
	
	if ($header->headerid != 0) {
		createDocumentLink();
	}
?>
<form class="contentform" method="post" action="savenewthrowawayquoteheader.php" onsubmit="return validate()">
	<div class="buttonContainer">
		<input type="button" class="dataButton" value="NOTES" id="btnHeanerNotes" name="btnHeanerNotes" />
		<input class="commandButton" type="submit" value="<?php if (($header->status == "N" || $header->status == "R") && $header->createdby == $_SESSION['SESS_MEMBER_ID']) echo "PROCEED"; else echo "ITEMS"; ?>" />
	</div>
	
	<div>
		
		<label>Site<label>
		<?php createCombo("siteid", "id", "name", "jomon_sites"); ?>
		<br>
		
		<label>Customer Name<label>
		<input type="text" id="customer" name="customer" style="width:300px" value="<?php echo $header->customer; ?>" />
		<br>
	</div>
	
	<div class="modal" id="notesDialog">
		<label>NOTES</label>
		<textarea id="notespopup" name="notespopup" cols=180 rows=19><?php echo $header->notes; ?></textarea>
	</div>
	
	<input type="hidden" id="notes" name="notes" value="" />
	<script>
		$(document).ready(function() {
				if (<?php echo $header->headerid; ?> != 0) {
					$("#apptitle").html("Quote: <?php echo $header->prefix . sprintf("%04d", $header->headerid); ?>, Customer: <?php echo $header->customer; ?>");
				}
				
				$("#siteid").val("<?php echo $header->siteid; ?>");
				$("#customer").val("<?php echo $header->customer; ?>");
				$("#notes").val("<?php echo escape_notes($header->notes); ?>");
				
				<?php
					if (($header->status == "N" || $header->status == "R") && 
					    ($header->contactid == $_SESSION['SESS_MEMBER_ID'] || 
					     $header->createdby == $_SESSION['SESS_MEMBER_ID'])) {
					     	
						if (count($header->items) > 0) {
							echo '$("#siteid").attr("disabled", true);';
						}
						
					} else {
						echo '$("#siteid").attr("disabled", true);';
							
						if (! isUserInRole("ADMIN")) {
							echo '$("#customer").attr("disabled", true);';
						}
					}
				?>
				
				$("#notespopup").change(
						function() {
							$("#notes").val($("#notespopup").val());
						}
					);
	
				$("#notesDialog").dialog({
						autoOpen: false,
						modal: true,
						width: 800,
						title: "Notes",
						buttons: {
							Ok: function() {
								$(this).dialog("close");
							}
						}
					});
	
					
				/* Notes entry. */				
				$("#btnHeanerNotes").click(
						function() {
							$("#notesDialog").dialog("open");
						}
					);
			});
	
	
		function validate() {
			if ($("#siteid").val() == 0) {
				dtAlert("Site must be specified");
				return false;
			}
			
			if ($("#customer").val() == "") {
				dtAlert("Customer must be specified");
				return false;
			}
			
			return true;
		}
	</script>
</form>


<?php include("system-footer.php"); ?>