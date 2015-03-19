<?php 
	require_once("quotationitem.php");
	include("system-header.php"); 
	
	function saveNotes() {
		$header = $_SESSION['QUOTATION'];
		$header->notes = $_POST['pk1'];
	}
	
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
<div>
	<div class="buttonContainer">
		<input type="button" class="navButton" id="btnHeader" value="BACK" />
		<input type="button" class="dataButton" value="NOTES" id="btnHeaderNotes" name="btnHeaderNotes" />
		<input type="button" class="navButton" value="DOCUMENTS" onclick="viewDocument(<?php echo $header->headerid; ?>)" />
		<input class="dataButton" id="btnSave" type="button" value="SAVE QUOTE" />
		<input class="commandButton" id="btnCancel" type="button" value="CANCEL QUOTE" />
		<input class="actionButton" id="btnProceed" type="button" value="REQUEST APPROVAL" />
	</div>
	
	<form class="contentform" id="summaryform" method="post" action="savequotesummary.php" onsubmit="return validate()">
		<div>
			<label>COST CODE<label>
			<select id="costcode" name="costcode">
				<option value="" selected></option>
				<option value="CAPEXCCF">CAPEX DEAL RELATED CCF</option>
				<option value="CAPEXBESPOKE">CAPEX - BESPOKE</option>
				<option value="OPEXBESPOKE">OPEX - BESPOKE</option>
				<option value="OPEXCUSTOMERPO">OPEX - Customer PO</option>
			</select>
			<br>
			
			<div id="ccfdiv" style='display:none'>
				<label>CCF Number<label>
				<input type="text" id="ccf" name="ccf" style="width:100px" value="<?php echo $header->ccf; ?>" align=right />
				<br>
			</div>
			
			<div id="ccfpathdiv" style='display:none'>
				<label>CCF Path<label>
				<input type="text" id="ccfpath" name="ccfpath" style="width:400px" value="<?php echo $header->ccfpath; ?>" align=right />
			</div>	
			
			<div id="sungardpodiv" style='display:none'>
				<label>Sungard PO<label>
				<input type="text" id="sungardpo" name="sungardpo" style="width:200px" value="<?php echo $header->sungardpo; ?>" align=right />
				<br>
			</div>
			
			<div id="customerpodiv" style='display:none'>
				<label>Customer PO<label>
				<input type="text" id="customerpo" name="customerpo" style="width:200px" value="<?php echo $header->customerpo; ?>" align=right />
				<br>
			</div>

			<label>Cab Install Date<label>
			<input type="text" id="cabinstalldate" name="cabinstalldate" class="datepicker" value="<?php echo $header->cabinstalldate; ?>" />
			<br>
			
			<label>Contact<label>
			<?php createCombo("contactid", "member_id", "login", "jomon_members"); ?>
			<br>
			
			<label>Required By<label>
			<input type="text" id="requiredby" name="requiredby" class="datepicker" value="<?php echo $header->requiredbydate; ?>" />
			<br>
			
		</div>
		
		<div class="modal" id="notesDialog">
			<label>NOTES</label>
			<textarea id="notespopup" name="notespopup" cols=180 rows=19><?php echo $header->notes; ?></textarea>
		</div>
		
		<input type="hidden" id="notes" name="notes" value="" />
		<input type="hidden" id="forward" name="forward" value="" />
	</form>
	<script>
		$(document).ready(function() {
				if (<?php echo $header->headerid; ?> != 0) {
					$("#apptitle").html("Quote: <?php echo $header->prefix . sprintf("%04d", $header->headerid); ?>, Customer: <?php echo $header->customer; ?>");
				}
				
				
				/* Back to header. */
				$("#btnHeader").click(
						function() {
							$("#forward").val("newquoteitem.php");
							$("#summaryform").submit();
						}
					);
					
				$("#costcode").change(
						function() {
							if ($("#costcode").val() == "CAPEXCCF") {
								$("#ccfdiv").show();
								$("#ccfpathdiv").show();
								$("#customerpodiv").hide();
								$("#sungardpodiv").hide();
								
							} else if ($("#costcode").val() == "CAPEXBESPOKE" || $("#costcode").val() == "OPEXBESPOKE") {
								$("#ccfdiv").hide();
								$("#ccfpathdiv").hide();
								$("#customerpodiv").hide();
								$("#sungardpodiv").show();
								
							} else if ($("#costcode").val() == "OPEXCUSTOMERPO") {
								$("#ccfdiv").hide();
								$("#ccfpathdiv").hide();
								$("#customerpodiv").show();
								$("#sungardpodiv").show();

							} else {
								$("#ccfdiv").hide();
								$("#ccfpathdiv").hide();
								$("#customerpodiv").hide();
								$("#sungardpodiv").hide();
							}
						}
					);
					
				$("#btnProceed").click(
						function() {
							$("#forward").val("processquoteheader.php");
							$("#summaryform").submit();
						}
					);
					
				$("#btnSave").click(
						function() {
							$("#forward").val("savequoteheader.php");
							$("#summaryform").submit();
						}
					);
				
				$("#btnCancel").click(
						function() {
							$("#forward").val("cancelquoteheader.php");
							$("#summaryform").submit();
						}
					);
				
				
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
								call("saveNotes", { pk1: $("#notespopup").val() });
							}
						}
					});
	
					
				/* Notes entry. */				
				$("#btnHeaderNotes").click(
						function() {
							$("#notesDialog").dialog("open");
						}
					);
					
				$("#costcode").val("<?php echo $header->costcode; ?>").trigger("change");
				$("#contactid").val("<?php echo $header->contactid; ?>");
				$("#notes").val("<?php echo escape_notes($header->notes); ?>");
				
				<?php
					if (($header->status == "N" || $header->status == "R") && 
					    ($header->contactid == $_SESSION['SESS_MEMBER_ID'] || 
					     $header->createdby == $_SESSION['SESS_MEMBER_ID'])) {
					     	
						if (count($header->items) > 0) {
							echo '$("#siteid").attr("disabled", true);';
						}
						
					} else {
						if (! isUserInRole("ADMIN")) {
							echo '$("#costcode").attr("disabled", true);';
							echo '$("#contactid").attr("disabled", true);';
							echo '$("#ccf").attr("disabled", true);';
							echo '$("#ccfpath").attr("disabled", true);';
							echo '$("#customerpo").attr("disabled", true);';
							echo '$("#sungardpo").attr("disabled", true);';
							echo '$("#requiredby").attr("disabled", true);';
							echo '$("#cabinstalldate").attr("disabled", true);';
							
						} else {
							echo '$("#btnProceed").css("display", "none");';
						}
					}
				?>
			});
	
	
		function validate() {
			if ($("#forward").val() == "savequoteheader.php") {
				if ($("#cabinstalldate").val() != "" && ! isDate($("#cabinstalldate").val())) {
					dtAlert("CAB install date must be a valid date");
					return false;
				}
				
				if ($("#requiredby").val() != "" && ! isDate($("#requiredby").val())) {
					dtAlert("Required by must be a valid date");
					return false;
				}
				
			} else if ($("#forward").val() == "processquoteheader.php") {
				if ($("#costcode").val() == 0) {
					dtAlert("Cost code must be specified");
					return false;
				}
				
				if ($("#costcode").val() == "CAPEXCCF") {
					if ($("#ccf").val() == "") {
						dtAlert("CCF Number must be specified");
						return false;
					}
					
					if ($("#ccfpath").val() == "") {
						dtAlert("CCF Path must be specified");
						return false;
					}
					
				} else if ($("#costcode").val() == "OPEXCUSTOMERPO") {
					if ($("#customerpo").val() == "") {
						dtAlert("Customer PO must be specified");
						return false;
					}
				}
				
				if (! isDate($("#cabinstalldate").val())) {
					dtAlert("CAB install date by must be a valid date");
					return false;
				}
				
				if ($("#contactid").val() == 0) {
					dtAlert("Contact ID must be specified");
					return false;
				}

				if (! isDate($("#requiredby").val())) {
					dtAlert("Required by must be a valid date");
					return false;
				}
			}
			
			return true;
		}
	</script>
</div>


<?php include("system-footer.php"); ?>