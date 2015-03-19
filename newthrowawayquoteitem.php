<?php 
	require_once("quotationitem.php");
	include("system-header.php"); 
	include("confirmdialog.php");
	
	$header = null;
	
	function saveNotes() {
		$header = $_SESSION['QUOTATION'];
		$header->notes = $_POST['pk1'];
	}
	
	function delete() {
		$header = $_SESSION['QUOTATION'];
		$header->remove($_POST['pk1']);
	}
	
	function editableState($stateArray) {
		$header = $_SESSION['QUOTATION'];
		
		if (in_array($header->status, $stateArray) && ($header->contactid == $_SESSION['SESS_MEMBER_ID'] || $header->createdby == $_SESSION['SESS_MEMBER_ID'])) {
			echo 'button';		
		}
		else {
			echo 'hidden';
		}
	}
	
	function editableStateSubmit($stateArray) {
		$header = $_SESSION['QUOTATION'];
		
		if (in_array($header->status, $stateArray) && ($header->contactid == $_SESSION['SESS_MEMBER_ID'] || $header->createdby == $_SESSION['SESS_MEMBER_ID'])) {
			echo 'submit';		
		}
		else {
			echo 'hidden';
		}
	}
		
	$header = $_SESSION['QUOTATION'];
	
	if ($header->headerid != 0) {
		createDocumentLink();
	}
	
	createConfirmDialog("confirmdialog", "Remove item ?", "removeItem");
?>
<script>
	var currentItem = null;
	var currentDesc = null;
	var autoSubmit = false;
	
	function removeSelectedItem(index, desc) {
		currentItem = index;
		currentDesc = desc;
		
		$("#confirmdialog .confirmdialogbody").html("You are about to remove item <b><i>'"  + desc + "'</i></b>.<br>Are you sure ?");
		$("#confirmdialog").dialog("open");
	}
</script>
<form method="post" style="margin-bottom:10px">
	<table style="width:100%;" class="grid" cellspacing=0 cellpadding=0>
		<thead>
			<tr>
				<td width="3%"></td>
				<td width="3%"></td>
				<td width="54%">Description</td>
				<td width="10%" align=right style="padding-right:18px">Qty</td>
				<td width="15%" align=right style="padding-right:18px">Price</td>
				<td width="15%" align=right style="padding-right:18px">Total</td>
			</tr>
		</thead>
	</table>
	<div style="width:100%; height:180px; overflow-y: scroll">
		<table style="width:100%" class="grid" cellspacing=0 cellpadding=0>
			<?php
				$grandTotal = 0;
				
				for ($i = 0; $i < $header->itemCount(); $i++) {
					$item = $header->get($i);
					
					if (! $item->deleted) {
						$grandTotal = $grandTotal + $item->total;
						
						echo "<tr>\n";
						echo "<td width='3%'>";

						if ($header->status == "N" || $header->status == "R") {
							echo "<img height=16 title='Remove item'  src='images/delete.png' onclick='removeSelectedItem(" . $i . ", \"" . $item->productdesc . "\");' />";
						}

						echo "</td>\n";
						echo "<td width='3%'>";

						if (($header->status == "N" || $header->status == "R") && $item->productdesc != "Expedite Charge" && $item->productdesc != "Emergency Charge") {
							echo "<img height=16 src='images/edit.png' onclick='edit(\"" . $i . "\");' />";
						}

						echo "</td>\n";
						echo "<td width='54%'>" . $item->productdesc . "</td>\n";
						echo "<td width='10%' align=right>" . number_format($item->qty, 2) . "</td>\n";
						echo "<td width='15%' align=right>" . number_format($item->price, 2) . "</td>\n";
						echo "<td width='15%' align=right>" . number_format($item->total, 2) . "</td>\n";
						echo "</tr>\n";
					}
				}
			?>
		</table>
	</div>
	<table style="width:100%" class="grid" cellspacing=0 cellpadding=0>
		<tfoot>
			<tr>
				<td colspan=5 align=right style="padding-right:18px">Grand total: <span id='grandtotal'><?php echo number_format($grandTotal, 2); ?></span></td>
			</tr>
		</tfoot>
	</table>
</form>

<div class="buttonContainer">
	<input type="button" class="navButton" id="btnHeader" value="BACK" />
	<input type="BUTTON" class="dataButton" value="NOTES" id="btnNotes" name="btnNotes" />
	<input type="BUTTON" class="commandButton" id="btnAddToQuote" value="ADD TO QUOTE" />
	<input type="BUTTON" class="commandButton hidden" id="btnCancel" value="CANCEL ITEM" />
	<form method='post' action='savethrowawayquoteheader.php' onsubmit="return proceed(this)">
		<input class="dataButton" type="SUBMIT" value="SAVE QUOTE" />
	</form>
	<form method='post' action='cancelquoteheader.php' onsubmit="">
		<input class="commandButton" type="SUBMIT" value="CANCEL QUOTE" />
	</form>
</div>

<form class="contentform" id="quoteForm" name="quoteForm" method="post" action="savequote.php" onsubmit="return validate()">
	<input type="hidden" id="callback" name="callback" value="newthrowawayquoteitem.php" />
	<input type="hidden" id="type" name="type" value="" />
	<input type="hidden" id="itemnumber" name="itemnumber" value="" />
	<input type="hidden" id="topleveltype" name="topleveltype" value="" />
	
	<div>
		<div id="additem">
			<label>NEW ITEM<label>
			<?php createCombo("category", "id", "name", "jomon_categories", "WHERE parentcategoryid = 0"); ?>
		</div>
		
		<div id="longlinepanel" style="display:none">
			<label>TYPE<label>
			<select id="longlinepanelcategory" name="longlinepanelcategory" style="width:200px">
				<option value="0"></option>
			</select>
			
			<div id="copper" style="display:none">
				<label>Category</label>
				<select id="coppercat1" name="coppercat1" style="width:200px">
					<option value="0"></option>
				</select>
				
				<br>
				
				<label>product</label>
				<select id="copperproduct" name="copperproduct" style="width:400px">
					<option value="0"></option>
				</select>
				<input type="hidden" id="copperproductdesc" style="width:800px" name="copperproductdesc" value="" />
				
				<br>
				
				<label>Length</label>
				<select id="copperlength" name="copperlength">
					<option value="0"></option>
				</select>
				
				<label>Quantity</label>
				<input type="text" id="copperqty" name="copperqty" value="0" style="width:100px" align=right />
			</div>
			
			<div id="fibre" style="display:none">
				<label>Category</label>
				<select id="fibrecat1" name="fibrecat1" style="width:200px">
					<option value="0"></option>
				</select>
				
				<br>
				
				<label>product</label>
				<select id="fibreproduct" name="fibreproduct" style="width:400px">
					<option value="0"></option>
				</select>
				<input type="hidden" id="fibreproductdesc" style="width:800px" name="fibreproductdesc" value="" />
				
				<br>
				
				<label>Length</label>
				<select id="fibrelength" name="fibrelength">
					<option value="0"></option>
				</select>
				
				<label>Quantity</label>
				<input type="text" id="fibreqty" name="fibreqty" value="0" style="width:100px" align=right />
			</div>
		</div>
		
		<div id="ancillary" style="display:none">
			<input type="hidden" id="ancillaryproductdesc" style="width:300px" name="ancillaryproductdesc" value="" />
			
			<label>Category</label>
			<select id="ancillarycat1" name="ancillarycat1" style="width:200px">
				<option value="0"></option>
			</select>
			
			<br>
			
			<label>product</label>
			<select id="ancillaryproduct" name="ancillaryproduct" style="width:400px" ">
				<option value="0"></option>
			</select>
			
			<br>
			
			<label>Quantity</label>
			<input type="text" id="ancillaryqty" name="ancillaryqty" value="0" style="width:100px" align=right />
		</div>
		
		<div id="bespoketask" style="display:none">
			<label>NOTES</label>
			<textarea id="bespokenotes" name="bespokenotes" cols=70 rows=13></textarea>
			
			<br>
			
			<label>PRICE</label>
			<input type="text" id="bespokeprice" name="bespokeprice" value="0" style="width:100px" align=right />
		</div>
		
		<div id="labourtask" style="display:none">
			<label>task</label>
			<select id="labourtaskproduct" name="labourtaskproduct" style="width:400px" onchange="$('#labourtaskproductdesc').val($(this).find('option:selected').text());">
				<option value="0"></option>
			</select>
			<input type="hidden" id="labourtaskproductdesc" style="width:800px" name="labourtaskproductdesc" value="" />
			
			<br>
			
			<label>TIME UNIT</label>
			<select id="labourrateunit" name="labourrateunit">
				<option value="0">Days</option>
				<option value="1">Hours</option>
			</select>
			
			<br />
			
			<label id="labourunittitle">man days</label>
			<input type="text" id="labourtaskqty" name="labourtaskqty" value="0" style="width:100px" align=right />
			
			<br>
			
			<label>In / out of hours</label>
			<select id="labourratehours" name="labourratehours">
				<option value="IN">In</option>
				<option value="OUT">Out</option>
			</select>
		</div>
		
		<div id="dummyPanel" style="display:none"></div>
		<div id="dummyLongLine" style="display:none"></div>
		
		<div class="modal" id="notesDialog">
			<label>NOTES</label>
			<textarea id="notespopup" name="notespopup" cols=180 rows=19><?php echo $header->notes; ?></textarea>
		</div>
		
		<input type="hidden" id="notes" name="notes" value="" />
		
		<div class="modal" id="additionalInfoDialog">
			<label>additional information</label>
			<div id="additionalInfoPanel">
			</div>
		</div>
		
		<br>
	</div>
</form>
<script>
	function proceed(form) {
		if (<?php echo $header->itemCount(); ?> == 0) {
			dtAlert("No items have been added");
			return false;
		}
		
		if (isValid(false)) {
			return true;
		}
		
		return false;
	}
	
	function showSundryProducts() {
		
	}	
	
	function getTasks(selectid, callback) {
		getJSONData('findtask.php', selectid, callback);
	}
	
	function getCategories(selectid, id, callback) {
		getJSONData('findcategory.php?id=' + id, selectid, callback);
	}
	
	function getProducts(selectid, id, callback) {
		getJSONData('findproduct.php?id=' + id, selectid, callback);
	}
	
	function getLengths(selectid, id, callback) {
		getJSONData('findproductlength.php?id=' + id, selectid, callback);
	}
	
	function showLongLineDestinations(qty) {
		var table = "<div id='longLineForm'>";
		var areas = "<?php createComboOptions("id", "name", "jomon_areas", "WHERE siteid = " . $header->siteid); ?>";
		
		for (var i = 1; i <= qty; i++) {
			table += "<div class='longlineadditionalitem' id='longlinediv_" + i + "'>";
			table += "<label style='float:left; width:150px; display:inline'>From Area</label>";
			
			table += "<select id='from_areaid' name='from_areaid[]'>" +
					 areas +
					 "</select>";
			
			table += "<label style='padding-left: 40px; width:120px; display:inline'> Cabinet </label>";
			table += "<input type='text' id='from_cabinet' name='from_cabinet[]' style='width:50px'></input><br>";
			
			table += "<label style='float:left; width:150px; display:inline'>To Area</label>";
			
			table += "<select id='to_areaid' name='to_areaid[]'>" +
					 areas +
					 "</select>";
					 
			table += "<label style='padding-left: 40px; width:120px; display:inline'> Cabinet </label>";
			table += "<input type='text' id='to_cabinet'  name='to_cabinet[]' style='width:50px'></input><br>";
			table += "<label style='float:left; width:150px; display:inline'>Installation Notes</label>";
			table += "<input type='text' cols=80 style='width:515px' id='to_presentation' name='to_presentation[]'></input><br><br>";
			table += "</div>";
		}
		
		table += "</div>";
		
		$("#additionalInfoPanel").html(table);
	}
	
	function showPanelDestinations(qty, qtyLoop) {
		var table = "<div id='panelForm' name='panelForm'>";
		var optPosition = "<option value='F'>Front</option><option value='R'>Rear</option>";
		var optLocation = "<option value='T'>Next Available 'U' From Top</option><option value='B'>Next Available 'U' From Bottom</option><option value='U'>'U' Location</option>";
		var areas = "<?php createComboOptions("id", "name", "jomon_areas", "WHERE siteid = " . $header->siteid); ?>";
		
		for (var loop = 0; loop < qtyLoop; loop++) {
			table += "<hr />";
			table += "<label>INSTALLATION INFO FOR ITEM " + (loop + 1) + "</label>";
			
			for (var i = 0; i < qty; i++) {

				if (i == 0) {
					table += "<div class='paneladditionalitem'><label style='float:left; width:90px; display:inline'>From Area</label>";
					table += "<select onchange='populateSibling(this)' loop='" + loop + "' id='from_areaid' name='from_areaid[]'>" + areas + "</select>";
					table += "<label style='padding-left: 15px; display:inline'>Cabinet</label>";
					table += "<input onchange='populateSibling(this)' loop='" + loop + "' type='text' id='from_cabinet'  name='from_cabinet[]' style='margin-left: 20px; width:50px'></input>";
					table += "<label style='padding-left: 15px;  display:inline'>Positioning </label>";
					table += "<select onchange='populateSibling(this)' loop='" + loop + "' id='from_position' name='from_position[]' style='width:80px'>" + optPosition + "</select>";
					table += "<label style='padding-left: 15px; display:inline'>Location </label>";
					table += "<select onchange='populateSibling(this); uLocationChange(this)' loop='" + loop + "' class='ulocation' id='from_location'  name='from_location[]' style='width:240px'>" + optLocation + "</select>";
					table += "<label style='display:none; padding-left: 15px; '> 'U'</label>";
					table += "<input onchange='populateSibling(this); uLocationChange(this)' loop='" + loop + "' type='text' id='from_uloc' name='from_uloc[]' style='display:none; width:30px'></input><br>";
					table += "</div>";
					
				} else {
					table += "<div class='paneladditionalitem' style='display:none'><label style='float:left; width:90px; display:inline'>From Area</label>";
					table += "<select onchange='populateSibling(this)' loop='" + loop + "' id='from_areaid' name='from_areaid[]'>" + areas + "</select>";
					table += "<label style='padding-left: 15px; display:inline'>Cabinet</label>";
					table += "<input onchange='populateSibling(this)' loop='" + loop + "' type='text' id='from_cabinet'  name='from_cabinet[]' style='margin-left: 20px; width:50px'></input>";
					table += "<label style='padding-left: 15px;  display:inline'>Positioning </label>";
					table += "<select onchange='populateSibling(this)' loop='" + loop + "' id='from_position' name='from_position[]' style='width:80px'>" + optPosition + "</select>";
					table += "<label style='padding-left: 15px; display:inline'>Location </label>";
					table += "<select onchange='populateSibling(this); uLocationChange(this)' loop='" + loop + "' class='ulocation' id='from_location'  name='from_location[]' style='width:240px'>" + optLocation + "</select>";
					table += "<label style='display:none; padding-left: 15px; '> 'U'</label>";
					table += "<input onchange='populateSibling(this); uLocationChange(this)' loop='" + loop + "' type='text' id='from_uloc' name='from_uloc[]' style='display:none; width:30px'></input><br>";
					table += "</div>";
				}
				
				table += "<div class='paneladditionalitem'>";
				
				table += "<label style='float:left; width:90px; display:inline'>To Area</label>";
				table += "<select id='to_areaid' name='to_areaid[]'>" + areas + "</select>";
				table += "<label style='padding-left: 15px; display:inline'>Cabinet</label>";
				table += "<input type='text' id='to_cabinet'  name='to_cabinet[]' style='margin-left: 20px; width:50px'></input>";
				table += "<label style='padding-left: 15px; display:inline'>Positioning </label>";
				table += "<select id='to_position'  name='to_position[]' style='width:80px'>" + optPosition + "</select>";
				table += "<label style='padding-left: 15px; display:inline'>Location </label>";
				table += "<select onchange='uLocationChange(this)' class='ulocation' id='to_location' name='to_location[]' style='width:240px'>" + optLocation + "</select>";
				table += "<label style='display:none; padding-left: 15px; '> 'U'</label>";
				table += "<input type='text' id='to_uloc' name='to_uloc[]' style='display:none; width:30px'></input><br>";
				table += "</div>";
			}
		}

		table += "</div>";
		
					
		
		
		$("#additionalInfoPanel").html(table);
	}
	
	function uLocationChange(widget) {
		if ($(widget).val() == "U") {
			$(widget).next().css('display', 'inline');
			$(widget).next().next().show();
			
		} else {
			$(widget).next().hide();
			$(widget).next().next().hide();
		}
	}
	
	function validate() {
		if ($("#category").find('option:selected').text() == "Copper") {
			if($("#coppercat1").val() == 0) {
				dtAlert("Category must be specified");
				$("#coppercat1").focus();
				return false;
			}
		}
		
		return true;
	}
	
	function removeItem() {
		call("delete", {pk1: currentItem});
	}
	
	$(document).ready(function() {
			if (<?php echo $header->headerid; ?> != 0) {
				$("#apptitle").html("Quote: <?php echo $header->prefix . sprintf("%04d", $header->headerid); ?>, Customer: <?php echo $header->customer; ?>");
			}
			
			<?php
				if ($header->status == "A" || 
					$header->status == "S" || 
					$header->status == "I" || 
					$header->status == "Q" ||
					$header->status == "C" ||
					$header->status == "V") {
						echo "$('#additem').css('visibility', 'hidden');\n";
						echo "$('#btnAddToQuote').css('display', 'none');\n";
						echo "$('#btnAdditionalInfo').css('display', 'none');\n";
				}
			?>
			
			$("#notesDialog").dialog({
					autoOpen: false,
					modal: true,
					width: 800,
					height: 500,
					title: "Notes",
					buttons: {
						Ok: function() {
							call("saveNotes", { pk1: $("#notespopup").val() });
						},
						Cancel: function() {
							$(this).dialog("close");
						}
					}
				});
				
			$("#additionalInfoDialog").dialog({
					autoOpen: false,
					modal: true,
					width: 930,
					height: 480,
					buttons: {
						Ok: function() {
							error = false;
							
							$("#additionalInfoDialog select").each(
									function() {
										if ($(this).val() == 0 && ! error) {
											error = true;
											dtAlert($(this).prev().html() + " has not been selected");
											$(this).focus();
										}
									}
								);
							
							$("#additionalInfoDialog input").each(
									function() {
										if ($(this).val() == "" && ! error && $(this).css("display") != "none") {
											error = true;
											dtAlert($(this).prev().html() + " has not been entered");
											$(this).focus();
										}
									}
								);
								
							if (! error) {
								$(this).dialog("close");
								
								if (autoSubmit == true) {
									addToQuote();
								}
							}
						},
						Cancel: function() {
							$(this).dialog("close");
						}
					}
				});
				
			/* Top level category change event. */
			$("#category").change(
					function() {
						/* Close all data type DIVs */
						$("#longlinepanel").hide();
						$("#ancillary").hide();
						$("#labourtask").hide();
						$("#bespoketask").hide();
						
						$("#type").val($("#category").find('option:selected').text());
						$("#topleveltype").val($("#category").find('option:selected').text());
						
						/* Set the top level type. */
						if ($("#category").find('option:selected').text() == "Panel Link" ||
						    $("#category").find('option:selected').text() == "Longlines") {
							/* Panels. */
							getCategories(
									"#longlinepanelcategory",
									$("#category").val(), 
									function() {
										$("#coppercat1").val(0);
										$("#copperproduct").val(0);
										$("#copperlength").val(0);
										$("#copperqty").val("0");
										$("#fibrecat1").val(0);
										$("#fibreproduct").val(0);
										$("#fibrelength").val(0);
										$("#fibreqty").val("0");
										
										$("#longlinepanel").show();
									}
								);
								
					    	$("#btnAdditionalInfo").attr("disabled", false);

						} else if ($("#category").find('option:selected').text() == "Patchleads") {
							/* Patch leads. */
							getCategories(
									"#longlinepanelcategory",
									$("#category").val(), 
									function() {
										$("#coppercat1").val(0);
										$("#copperproduct").val(0);
										$("#copperlength").val(0);
										$("#copperqty").val("0");
										$("#fibrecat1").val(0);
										$("#fibreproduct").val(0);
										$("#fibrelength").val(0);
										$("#fibreqty").val("0");
										
										$("#longlinepanel").show();
									}
								);
								
							$("#btnAdditionalInfo").attr("disabled", true);
								
						} else if ($("#category").find('option:selected').text() == "Sundry Items") {
							$("#btnAdditionalInfo").attr("disabled", true);
							
							getCategories(
									"#ancillarycat1",
									$("#category").val(), 
									function() {
										$("#ancillaryproduct").val(0);
										$("#ancillaryqty").val("0");
										$("#ancillary").show();
									}
								);
								
						} else if ($("#category").find('option:selected').text() == "Bespoke") {
							$("#btnAdditionalInfo").attr("disabled", true);
							$("#bespoketask").show();
								
						} else if ($("#category").find('option:selected').text() == "Emergency Charge") {
							$("#btnAdditionalInfo").attr("disabled", true);
								
						} else if ($("#category").find('option:selected').text() == "Expedite Charge") {
							$("#btnAdditionalInfo").attr("disabled", true);
								
						} else if ($("#category").find('option:selected').text() == "Labour Task") {
							$("#btnAdditionalInfo").attr("disabled", true);
							
							getTasks(
									"#labourtaskproduct",
									function() {
										$("#labourtask").show();
									}
								);
						} 	
						
						$("#submit").show();
						$("#btnNotes").show();
					}
				);
				
			/* Long line / Panel category change. */				
			$("#longlinepanelcategory").change(
					function() {
						$("#copper").hide();
						$("#fibre").hide();
						
						$("#type").val($("#longlinepanelcategory").find('option:selected').text());
						
						if ($("#longlinepanelcategory").find('option:selected').text() == "Copper Panels" ||
						    $("#longlinepanelcategory").find('option:selected').text() == "Copper") {
							/* Copper. */
							getCategories(
									"#coppercat1",
									$("#longlinepanelcategory").val(), 
									function() {
										$("#copper").show();
										$("#coppercat1").val(0);
										$("#copperproduct").val(0);
										$("#copperlength").val(0);
										$("#copperqty").val("0");
										$("#coppercat1").focus();
									}
								);
								
						} else if ($("#longlinepanelcategory").find('option:selected').text() == "Fibre Panels" ||
						           $("#longlinepanelcategory").find('option:selected').text() == "Fibre") {
							/* Copper. */
							getCategories(
									"#fibrecat1",
									$("#longlinepanelcategory").val(), 
									function() {
										$("#fibre").show();
										$("#fibrecat1").val("0");
										$("#fibreproduct").val("0");
										$("#fibrelength").val("0");
										$("#fibreqty").val("0");
										$("#fibrecat1").focus();
									}
								);
						}
					}
				);
				
			/* Copper category. */
			$("#coppercat1").change(
					function() {
						getProducts(
								"#copperproduct",
								$("#coppercat1").val(), 
								function() {
								}
							);
					}
				);
				
			$("#labourrateunit").change(
					function() {
						if ($(this).val() == "1") {
							$("#labourunittitle").html("Manned hours");

						} else {
							$("#labourunittitle").html("Manned days");
						}
					}
				);
				
			/* Fibre product change. */
			$("#fibreproduct").change(
					function() {
						$('#fibreproductdesc').val($("#fibreproduct").find('option:selected').text());
						
						getLengths(
								"#fibrelength",
								$("#fibreproduct").val(), 
								function() {
								}
							);
					}
				);
				
			/* Copper product change. */
			$("#copperproduct").change(
					function() {
						$('#copperproductdesc').val($("#copperproduct").find('option:selected').text());
						
						getLengths(
								"#copperlength",
								$("#copperproduct").val(), 
								function() {
								}
							);
					}
				);
				
			/* Ancillary product change. */
			$("#ancillaryproduct").change(
					function() {
						$('#ancillaryproductdesc').val($("#ancillaryproduct").find('option:selected').text());
					}
				);

			/* Fibre category change. */				
			$("#ancillarycat1").change(
					function() {
						getProducts(
								"#ancillaryproduct",
								$("#ancillarycat1").val(), 
								function() {
								}
							);
					}
				);

			/* Fibre category change. */				
			$("#fibrecat1").change(
					function() {
						getProducts(
								"#fibreproduct",
								$("#fibrecat1").val(), 
								function() {
								}
							);
					}
				);

			/* Labour task product. */				
			$("#labourtaskproduct").change(
					function() {
						$('#labourtaskproductdesc').val($(this).find('option:selected').text());
					}
				);
				
			/* Back to header. */
			$("#btnHeader").click(
					function() {
						window.location.href = 'newthrowawayquote.php';
					}
				);
				
			/* Notes entry. */				
			$("#btnNotes").click(
					function() {
						$("#notesDialog").dialog("open");
					}
				);
				
			/* Additional Info. */
			$("#btnAdditionalInfo").click(
					function() {
						if (! isValid(true)) {
							return;
						}
						
						if ($("#category").find('option:selected').text() == "Longlines") {
							/* Show long line destinations. */
							
							if ($("#additionalInfoPanel").html() == "") {
								if ($("#type").val() == "Copper") {
									showLongLineDestinations($("#copperqty").val());
									
								} else {
									showLongLineDestinations($("#fibreqty").val());
								}
							}
							
							/* Open the dialog. */
							$("#additionalInfoDialog").dialog("open");
							
						} else if ($("#topleveltype").val() == "Panel Link") {
							/* Show panel destinations. */
							if ($("#additionalInfoPanel").html() == "") {
								if ($("#type").val() == "Copper") {
									if ($('#copperproductdesc').val().indexOf("7 Panels Link") != -1) {
										showPanelDestinations(7, $("#copperqty").val());
										
									} else if ($('#copperproductdesc').val().indexOf("Four Panels Link") != -1) {
										showPanelDestinations(4, $("#copperqty").val());
										
									} else if ($('#copperproductdesc').val().indexOf("Two Panels Link") != -1) {
										showPanelDestinations(2, $("#copperqty").val());
										
									} else {
										showPanelDestinations(1, $("#copperqty").val());
									}
									
								} else {
									if ($('#fibreproductdesc').val().indexOf("7 Panel Link") != -1) {
										showPanelDestinations(7, $("#fibreqty").val());
										
									} else if ($('#fibreproductdesc').val().indexOf("Four Panel Link") != -1) {
										showPanelDestinations(4, $("#fibreqty").val());
										
									} else if ($('#fibreproductdesc').val().indexOf("Two Panel Link") != -1) {
										showPanelDestinations(2, $("#fibreqty").val());
										
									} else {
										showPanelDestinations(1, $("#fibreqty").val());
									}
								}
							}
							
							/* Open the dialog. */
							$("#additionalInfoDialog").dialog("open");
						}
					}
				);
				
			/* Changes regarding reset of additonal information. */
			$("#fibreqty, #copperqty, #fibreproduct, #copperproduct").change(
					function() {
						$("#additionalInfoPanel").html("");
					}
				);
				
			$("#fibreproduct, #copperproduct").change(
					function() {
						$("#additionalInfoPanel").html("");
					}
				);
				
			$("#btnCancel").click(
					function() {
						$("#confirmupdate").hide();
						$("#category").val(0).trigger("change");
						$("#btnProceed").attr("disabled", false);
						$("#btnAddToQuote").val("ADD TO QUOTE");
						$("#btnCancel").hide();
						$("#itemnumber").val("");
					}
				);
				
			/* Move the notes to within the form. */
			$("#notes").change(
					function() {
						$("#notes").val($("#dialogNote").val());
					}
				);
				
			/* Add to Quote. */
			$("#btnAddToQuote").click(
					function() {
						if (isValid(true)) {
							addToQuote();
						}
					}
				);
				
		});
		
		
	function addToQuote() {
		var submitFunction = function() {
				$("#longLineForm").appendTo("#dummyLongLine");
				$("#panelForm").appendTo("#dummyPanel");
				$("#quoteForm").submit();
			};
		
		if ($("#category").find('option:selected').text() == "Panel Link") {
			dtAlert("Are cable management bars required. If so please add under sundry items", submitFunction);
			
		} else {
			submitFunction();
		}
	}
		
	function addToQuote() {
		var submitFunction = function() {
				$("#longLineForm").appendTo("#dummyLongLine");
				$("#panelForm").appendTo("#dummyPanel");
				$("#quoteForm").submit();
			};
		
		if ($("#category").find('option:selected').text() == "Panel Link") {
			dtAlert("Are cable management bars required. If so please add under sundry items", submitFunction);
			
		} else {
			submitFunction();
		}
	}
		
	function isValid(checkTopLevel) {
		autoSubmit = false;

		if (checkTopLevel) {
			if ($("#category").val() == 0) {
				dtAlert("Item must be selected");
				return false;
			}
		}		
		if ($("#category").find('option:selected').text() == "Emergency Charge" ||
		    $("#category").find('option:selected').text() == "Expedite Charge") {
		    
		    if (<?php
		    $found = "false";
		    
			for ($i = 0; $i < $header->itemCount(); $i++) {
				$item = $header->get($i);
				
				if (! $item->deleted && ($item->productdesc == "Emergency Charge" || $item->productdesc == "Expedite Charge")) {
					$found = "true";
					break;
				}
			}
			
			echo $found;
			?>) {
				dtAlert("Only one charge can be added to each quote.");
				return false;
	
			}
		}
		
		if ($("#category").find('option:selected').text() == "Panel Link" ||
		    $("#category").find('option:selected').text() == "Patchleads" ||
		    $("#category").find('option:selected').text() == "Longlines") {
		    	
		    if ($("#longlinepanelcategory").val() == 0) {
				dtAlert("Type must be selected");
				return false;
		    }
		    
			if ($("#longlinepanelcategory").find('option:selected').text() == "Copper Panels" ||
			    $("#longlinepanelcategory").find('option:selected').text() == "Copper") {
			    	
			    if ($("#coppercat1").val() == 0) {
			    	dtAlert("Category must be specified");
			    	return false;
			    }
			    	
			    if ($("#copperproduct").val() == 0) {
			    	dtAlert("Product must be specified");
			    	return false;
			    }
			    	
			    if ($("#copperlength").val() == 0) {
			    	dtAlert("Length must be specified");
			    	return false;
			    }
			    	
			    if ($("#copperqty").val() == "0") {
			    	dtAlert("Length must be specified");
			    	return false;
			    }
			    
			} else if ($("#longlinepanelcategory").find('option:selected').text() == "Fibre Panels" ||
					   $("#longlinepanelcategory").find('option:selected').text() == "Fibre") {
			
			    if ($("#fibrecat1").val() == 0) {
			    	dtAlert("Category must be specified");
			    	return false;
			    }
			    	
			    if ($("#fibreproduct").val() == 0) {
			    	dtAlert("Product must be specified");
			    	return false;
			    }
			    	
			    if ($("#fibrelength").val() == 0) {
			    	dtAlert("Length must be specified");
			    	return false;
			    }
			    	
			    if ($("#fibreqty").val() == "0") {
			    	dtAlert("Quantity must be specified");
			    	return false;
			    }
			}
			
		} else if ($("#category").find('option:selected').text() == "Sundry Items") {
		    if ($("#ancillarycat1").val() == 0) {
		    	dtAlert("Category must be specified");
		    	return false;
		    }
		    	
		    if ($("#ancillaryproduct").val() == 0) {
		    	dtAlert("Product must be specified");
		    	return false;
		    }
		    
		    if ($("#ancillaryqty").val() == "0") {
		    	dtAlert("Quantity must be specified");
		    	return false;
		    }
			
		} else if ($("#category").find('option:selected').text() == "Labour Task") {
		    if ($("#labourtaskproduct").val() == 0) {
		    	dtAlert("Product must be specified");
		    	return false;
		    }
		    
		    if ($("#labourtaskqty").val() == "0") {
		    	dtAlert("Quantity must be specified");
		    	return false;
		    }
		}
		
		return true;
	}
		
	function edit(item) {
		$("#itemnumber").val(item);
		$("#btnAddToQuote").val("UPDATE ITEM");
		$("#btnCancel").removeClass("hidden");
		$("#btnCancel").show();
		
		callAjax(
				"updatequoteitem.php", 
				{
					item: item 
				},
				function(data) {
					var row = data[0];

					$("#category").val(row.cat1).trigger("change");
					
					if ($("#category").find('option:selected').text() == "Panel Link" ||
					    $("#category").find('option:selected').text() == "Patchleads" ||
					    $("#category").find('option:selected').text() == "Longlines") {
					    	
						$("#longlinepanelcategory").val(row.cat2).trigger("change");

						if ($("#longlinepanelcategory").find('option:selected').text() == "Copper Panels" ||
						    $("#longlinepanelcategory").find('option:selected').text() == "Copper") {
							/* Copper. */
							$("#coppercat1").val(row.cat3).trigger("change");
							$("#copperproduct").val(row.product).trigger("change");
							$("#copperlength").val(row.length).trigger("change");
							$("#copperqty").val(row.qty).trigger("change");
							
						} else if ($("#longlinepanelcategory").find('option:selected').text() == "Fibre Panels" ||
								   $("#longlinepanelcategory").find('option:selected').text() == "Fibre") {
								   	
							/* Fibre. */
							$("#fibrecat1").val(row.cat3).trigger("change");
							$("#fibreproduct").val(row.product).trigger("change");
							$("#fibrelength").val(row.length).trigger("change");
							$("#fibreqty").val(row.qty).trigger("change");
						}
						
						if ($("#category").find('option:selected').text() == "Longlines") {
							populateLongLineInstallationInfo();
							
							if (row.longline) {
								for (i = 0; i < row.longline.length; i++) {
									$($("#longLineForm select[name^=from_areaid]")[i]).val(row.longline[i].fromarea).trigger("change");
									$($("#longLineForm input[name^=from_cabinet]")[i]).val(row.longline[i].fromcabinet).trigger("change");
									$($("#longLineForm select[name^=to_areaid]")[i]).val(row.longline[i].toarea).trigger("change");
									$($("#longLineForm input[name^=to_cabinet]")[i]).val(row.longline[i].tocabinet).trigger("change");
									$($("#longLineForm input[name^=to_presentation]")[i]).val(row.longline[i].notes).trigger("change");
								}
							}

						} else if ($("#category").find('option:selected').text() == "Panel Link") {
							populatePanelInstallationInfo();
							
							if (row.panels) {
								for (i = 0; i < row.panels.length; i++) {
									$($("#panelForm select[name^=from_areaid]")[i]).val(row.panels[i].fromarea).trigger("change");
									$($("#panelForm input[name^=from_cabinet]")[i]).val(row.panels[i].fromcabinet).trigger("change");
									$($("#panelForm select[name^=from_position]")[i]).val(row.panels[i].fromposition).trigger("change");
									$($("#panelForm select[name^=from_location]")[i]).val(row.panels[i].fromlocation).trigger("change");
									$($("#panelForm input[name^=from_uloc]")[i]).val(row.panels[i].fromulocation).trigger("change");
									$($("#panelForm select[name^=to_areaid]")[i]).val(row.panels[i].toarea).trigger("change");
									$($("#panelForm input[name^=to_cabinet]")[i]).val(row.panels[i].tocabinet).trigger("change");
									$($("#panelForm select[name^=to_position]")[i]).val(row.panels[i].toposition).trigger("change");
									$($("#panelForm select[name^=to_location]")[i]).val(row.panels[i].tolocation).trigger("change");
									$($("#panelForm input[name^=to_uloc]")[i]).val(row.panels[i].toulocation).trigger("change");
								}
							}
						}
						
					} else if ($("#category").find('option:selected').text() == "Sundry Items") {
						$("#ancillarycat1").val(row.cat2).trigger("change");
						$("#ancillaryproduct").val(row.product).trigger("change");
						$("#ancillaryproductdesc").val(row.description).trigger("change");
						$("#ancillaryqty").val(row.qty);
						
					} else if ($("#category").find('option:selected').text() == "Labour Task") {
						$("#labourtaskproduct").val(row.product).trigger("change");
						$("#labourrateunit").val(row.cat2).trigger("change");
						$("#labourtaskqty").val(row.qty);
						$("#labourratehours").val(row.labourratehours);
						
					} else if ($("#category").find('option:selected').text() == "Bespoke") {
						$("#bespokenotes").val(row.notes);
						$("#bespokeprice").val(row.price);
					}
				}
			);
	}
	
	function populateLongLineInstallationInfo() {
		if ($("#type").val() == "Copper") {
			showLongLineDestinations($("#copperqty").val());
			
		} else {
			showLongLineDestinations($("#fibreqty").val());
		}
	}
	
	function populatePanelInstallationInfo() {
		if ($("#type").val() == "Copper") {
			if ($('#copperproductdesc').val().indexOf("7 Panels Link") != -1) {
				showPanelDestinations(7, $("#copperqty").val());
				
			} else if ($('#copperproductdesc').val().indexOf("Four Panels Link") != -1) {
				showPanelDestinations(4, $("#copperqty").val());
				
			} else if ($('#copperproductdesc').val().indexOf("Two Panels Link") != -1) {
				showPanelDestinations(2, $("#copperqty").val());
				
			} else {
				showPanelDestinations(1, $("#copperqty").val());
			}
			
		} else {
			if ($('#fibreproductdesc').val().indexOf("7 Panel Link") != -1) {
				showPanelDestinations(7, $("#fibreqty").val());
				
			} else if ($('#fibreproductdesc').val().indexOf("Four Panel Link") != -1) {
				showPanelDestinations(4, $("#fibreqty").val());
				
			} else if ($('#fibreproductdesc').val().indexOf("Two Panel Link") != -1) {
				showPanelDestinations(2, $("#fibreqty").val());
				
			} else {
				showPanelDestinations(1, $("#fibreqty").val());
			}
		}
	}
	
	function populateSibling(element) {
		$("#panelForm #" + element.id + "[loop='" + $(element).attr('loop') + "']").each(
				function (i) {
					$(this).val($(element).val());
				}
			);
		
	}
</script>
<?php include("system-footer.php"); ?>