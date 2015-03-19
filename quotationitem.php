<?php
	class PanelItem {
	    public $item = array();
	    
	    public function get($item) {
	    	return $this->item[$item];
	    }
	    
	    public function count() { 
	    	return count($this->item);
	    }
	    
	    public function add($item) { 
	    	$this->item[count($this->item)] = $item;
	    }
	}
	
	class LongLineItem {
	    public $item = array();
	    
	    public function get($item) {
	    	return $this->item[$item];
	    }
	    
	    public function count() { 
	    	return count($this->item);
	    }
	    
	    public function add($item) { 
	    	$this->item[count($this->item)] = $item;
	    }
	}
	
	class PanelSubItem {
	    public $fromarea = "";
	    public $fromareaname = "";
	    public $fromcabinet = "";
	    public $fromposition;
	    public $frompositionname;
	    public $fromlocation;
	    public $fromlocationname;
	    public $fromulocation;
	    public $toarea = "";
	    public $toareaname = "";
	    public $tocabinet = "";
	    public $toposition;
	    public $topositionname;
	    public $tolocation;
	    public $tolocationname;
	    public $toulocation;
	}
	
	class LongLineSubItem {
	    public $fromarea = "";
	    public $fromareaname = "";
	    public $fromcabinet = "";
	    public $toarea = "";
	    public $toareaname = "";
	    public $tocabinet = "";
	    public $notes = "";
	}
	
	class QuotationItem {
	    // property declaration
	    public $id = 0;
	    public $type = "";
	    public $cat1 = 0;
	    public $cat2 = 0;
	    public $cat3 = 0;
	    public $productid = 0;
	    public $productdesc = "";
	    public $qty = 0;
	    public $itemnumber = 0;
	    public $price = 0;
	    public $total = 0;
	    public $length = 0;
	    public $productlengthid = 0;
	    public $inout = "";
	    public $notes = "";
	    public $deleted = false;
	    public $longline= null;
	    public $panel= null;
	    public $status = "N";
	    public $manneddays = 0;
	}
	
	class QuotationHeader {
	    public $siteid = 0;
	    public $contactid = 0;
	    public $costcode = "";
	    public $oldcostcode = "";
	    public $costcodedesc = "";
	    public $customer = "";
	    public $ccf = "";
	    public $customerpo = "";
	    public $sungardpo = "";
	    public $ccfpath = "";
	    public $cabinstalldate = "";
	    public $requiredbydate = "";
	    public $oldnotes = "";
	    public $notes = "";
	    public $status = "";
	    public $originalstatus = "";
	    public $originalthrowawayquoteid = 0;
	    public $items = array();
	    public $headerid = 0;
	    public $approvalid = 0;
	    public $approvalrequesteddate = 0;
	    public $createdby = "";
	    public $createddate = 0;
	    public $approvedby = 0;
	    public $approveddate = 0;
	    public $scheduledby =  0;
	    public $scheduleddate = 0;
	    public $ceapprovedby =  0;
	    public $ceapproveddate = 0;
	    public $archivedby = 0;
	    public $archiveddate = 0;
	    public $completedby = 0;
	    public $completeddate = 0;
	    public $cancelleddate = 0;
	    public $cancelledby = 0;
	    public $internalapprovalcode = "";
	    public $qaby = 0;
	    public $qadate = 0;
	    public $jobprefix = "";
	    public $prefix = "";
	    public $jobid = 0;

	    public function get($item) {
	    	return $this->items[$item];
	    }
	    
	    public function itemCount() { 
	    	return count($this->items);
	    }
	    
	    public function liveCount() { 
			$counter = 0;
			
			for ($i = 0; $i <count($this->items); $i++) {
				if (! $this->items[$i]->deleted) {
					$counter++;
				}
			}
			
			return $counter;
	    }
	    
	    public function add($item) { 
	    	$this->items[count($this->items)] = $item;
	    }
	    
	    public function remove($index) { 
	    	$this->items[$index]->deleted = true;
	    	$this->recalculateCharges();
	    }
	    
	    public function recalculateCharges() {
			$total = 0;
			
			/* Find the total for all other products. */
			for ($i = 0; $i < $this->itemCount(); $i++) {
				$item = $this->get($i);
				
				if ($item->deleted) {
					continue;
				}
				
				if ($item->productdesc != "Emergency Charge" && $item->productdesc != "Expedite Charge") {
					$total = $total + $item->total;
				}
			}
			
			for ($i = 0; $i < $this->itemCount(); $i++) {
				$item = $this->get($i);
				
				if ($item->deleted) {
					continue;
				}
				
				if ($item->productdesc == "Emergency Charge") {
					$item->price = $total * 0.25;
					$item->total = $item->price;
					
					break;
				}
				
				if ($item->productdesc == "Expedite Charge") {
					$item->price = $total * 0.17;
					$item->total = $item->price;
					
					break;
				}
			}
	    }
	    
	    public function getEmailDetails() {
	    	$email = "";
			$email = $email .  "<table class='headinggroup' style='margin-bottom:20px'>\n";
			$email = $email .  "<tr>\n";
			$email = $email .  "<td class='label'>Raised by</td>\n";
			$email = $email .  "<td>" . GetUserName($this->createdby) . "</td>\n";
			$email = $email .  "</tr>\n";
			$email = $email .  "<tr>\n";
			
			if ($this->status == "N" || $this->status == "R") {
				$email = $email .  "<td class='label'>Quote Number</td>\n";
				$email = $email .  "<td>" . $this->prefix . sprintf("%04d", $this->headerid) . "</td>\n";
						
			} else {
				$email = $email .  "<td class='label'>Job Number</td>\n";
				$email = $email .  "<td>" . $this->jobprefix . sprintf("%04d", $this->headerid) . "</td>\n";
			}
					
			$email = $email .  "</tr>\n";
			$email = $email .  "<tr>\n";
			$email = $email .  "<td class='label'>Required By</td>\n";
			$email = $email .  "<td>" . $this->requiredbydate . "</td>\n";
			$email = $email .  "</tr>\n";
			$email = $email .  "<tr>\n";
			$email = $email .  "<td class='label'>CCF/PO Number</td>\n";
			$email = $email .  "<td>" . $this->ccf . "</td>\n";
			$email = $email .  "</tr>\n";
			
			if ($this->internalapprovalcode != "") {
				$email = $email .  "<tr>\n";
				$email = $email .  "<td class='label'>Internal Budget Code</td>\n";
				$email = $email .  "<td>" . $this->internalapprovalcode . "</td>\n";
				$email = $email .  "</tr>\n";
			}
			
			$email = $email .  "<tr>\n";
			$email = $email .  "<td class='label'>Customer</td>\n";
			$email = $email .  "<td>" . $this->customer . "</td>\n";
			$email = $email .  "</tr>\n";
			$email = $email .  "<tr>\n";
			$email = $email .  "<td class='label'>Contact</td>\n";
			$email = $email .  "<td>" . GetUserName($this->contactid) . "</td>\n";
			$email = $email .  "</tr>\n";
			$email = $email .  "<tr>\n";
			$email = $email .  "<td class='label'>Site</td>\n";
			$email = $email .  "<td>" . GetSiteName($this->siteid) . "</td>\n";
			$email = $email .  "</tr>\n";
			$email = $email .  "<tr>\n";
			$email = $email .  "<tr>\n";
			$email = $email .  "<td class='label'>Cost Code</td>\n";
			$email = $email .  "<td>" . $this->costcodedesc . "</td>\n";
			$email = $email .  "</tr>\n";
			$email = $email .  "<tr>\n";
			$email = $email .  "<td class='label'>CAB Install Date</td>\n";
			$email = $email .  "<td>" . $this->cabinstalldate . "</td>\n";
			$email = $email .  "</tr>\n";
			
			if ($this->approvedby != 0) {
				if ($this->status == "R") {
					$email = $email .  "<tr>\n";
					$email = $email .  "<td class='label'>Rejected By</td>\n";
					$email = $email .  "<td>" . GetUserName($this->approvedby) . "</td>\n";
					$email = $email .  "</tr>\n";
					$email = $email .  "<tr>\n";
					$email = $email .  "<td class='label'>Rejected Date</td>\n";
					$email = $email .  "<td>" . $this->approveddate . "</td>\n";
					$email = $email .  "</tr>\n";
					
				} else {
					$email = $email .  "<tr>\n";
					$email = $email .  "<td class='label'>Verified By</td>\n";
					$email = $email .  "<td>" . GetUserName($this->approvedby) . "</td>\n";
					$email = $email .  "</tr>\n";
					$email = $email .  "<tr>\n";
					$email = $email .  "<td class='label'>Verified Date</td>\n";
					$email = $email .  "<td>" . $this->approveddate . "</td>\n";
					$email = $email .  "</tr>\n";
				}
			}
			
			if ($this->scheduledby != 0) {
				$email = $email .  "<tr>\n";
				$email = $email .  "<td class='label'>Scheduled By</td>\n";
				$email = $email .  "<td>" . GetUserName($this->scheduledby) . "</td>\n";
				$email = $email .  "</tr>\n";
				$email = $email .  "<tr>\n";
				$email = $email .  "<td class='label'>Scheduled Date</td>\n";
				$email = $email .  "<td>" . $this->scheduleddate . "</td>\n";
				$email = $email .  "</tr>\n";
			}
			
			if ($this->ceapprovedby != 0) {
				$email = $email .  "<tr>\n";
				$email = $email .  "<td class='label'>CE Approved By</td>\n";
				$email = $email .  "<td>" . GetUserName($this->ceapprovedby) . "</td>\n";
				$email = $email .  "</tr>\n";
				$email = $email .  "<tr>\n";
				$email = $email .  "<td class='label'>CE Approved Date</td>\n";
				$email = $email .  "<td>" . $this->ceapproveddate . "</td>\n";
				$email = $email .  "</tr>\n";
			}
			
			if ($this->completedby != 0) {
				$email = $email .  "<tr>\n";
				$email = $email .  "<td class='label'>Completed By</td>\n";
				$email = $email .  "<td>" . GetUserName($this->completedby) . "</td>\n";
				$email = $email .  "</tr>\n";
				$email = $email .  "<tr>\n";
				$email = $email .  "<td class='label'>Completed Date</td>\n";
				$email = $email .  "<td>" . $this->completeddate . "</td>\n";
				$email = $email .  "</tr>\n";
			}
			
			if ($this->qaby != 0) {
				$email = $email .  "<tr>\n";
				$email = $email .  "<td class='label'>QA By</td>\n";
				$email = $email .  "<td>" . GetUserName($this->qaby) . "</td>\n";
				$email = $email .  "</tr>\n";
				$email = $email .  "<tr>\n";
				$email = $email .  "<td class='label'>QA Date</td>\n";
				$email = $email .  "<td>" . $this->qadate . "</td>\n";
				$email = $email .  "</tr>\n";
			}
			
			if ($this->archivedby != 0) {
				$email = $email .  "<tr>\n";
				$email = $email .  "<td class='label'>Handedover By</td>\n";
				$email = $email .  "<td>" . GetUserName($this->archivedby) . "</td>\n";
				$email = $email .  "</tr>\n";
				$email = $email .  "<tr>\n";
				$email = $email .  "<td class='label'>Handedover Date</td>\n";
				$email = $email .  "<td>" . $this->archiveddate . "</td>\n";
				$email = $email .  "</tr>\n";
			}
			
			$email = $email .  "</table>\n";
			
			$email = $email . "<label>NOTES</label><br>";
			$email = $email . "<p>";
			$email = $email . str_replace("\n", "<br/>", $this->notes);
			$email = $email . "</p><br>";
			
	    	$email = $email . "<table style='width:100%' class='grid' cellspacing=0 cellpadding=0>";
			$email = $email . "<thead style='font-style:italic; font-weight:bold'>";
			$email = $email . "<tr>";
			$email = $email . "<td width='10%'>Qty</td>";
			$email = $email . "<td width='60%'>Description</td>";
			$email = $email . "<td width='15%' align=right>Price</td>";
			$email = $email . "<td width='15%' align=right>Total</td>";
			$email = $email . "</tr>";
			$email = $email . "</thead>";
			
			$grandTotal = 0;
			
			for ($i = 0; $i < $this->itemCount(); $i++) {
				$item = $this->get($i);
				
				if (! $item->deleted) {
					$grandTotal = $grandTotal + $item->total;
					
					$email = $email . "<tr>\n";
					$email = $email . "<td width='10%'>";
					$email = $email . number_format($item->qty, 2);
					$email = $email . "</td>\n";
					$email = $email . "<td width='60%'>" . $item->productdesc . "</td>\n";
					$email = $email . "<td width='15%' class='price' align=right>" . number_format($item->price, 2) . "</td>\n";
					$email = $email . "<td width='15%' class='total' align=right>" . number_format($item->total, 2) . "</td>\n";
					$email = $email . "</tr>\n";
					
					if ($item->panel != null && count($item->panel->item) > 0) {
						$email = $email . "<tr class='subtable'><td width='100%'colspan='5'><table cellpadding=0 cellspacing=0 width=100%>";
						$email = $email . "<thead style='font-style:italic; font-weight:bold'>";
						$email = $email . "<tr>";
						$email = $email . "<td>From Area</td>";
						$email = $email . "<td>From Cabinet</td>";
						$email = $email . "<td>From Position</td>";
						$email = $email . "<td>From Location</td>";
						$email = $email . "<td>From Uloc</td>";
						$email = $email . "<td>To Area</td>";
						$email = $email . "<td>To Cabinet</td>";
						$email = $email . "<td>To Position</td>";
						$email = $email . "<td>To Location</td>";
						$email = $email . "<td>To Uloc</td>";
						$email = $email . "</tr>";
						$email = $email . "</thead>";
						
						for ($x = 0; $x < count($item->panel->item); $x++) {
							$subitem = $item->panel->item[$x];
							
							$email = $email . "<tr>";
							$email = $email . "<td>" . $subitem->fromareaname . "</td>";
							$email = $email . "<td>" . $subitem->fromcabinet . "</td>";
							$email = $email . "<td>" . $subitem->frompositionname . "</td>";
							$email = $email . "<td>" . $subitem->fromlocationname . "</td>";
							$email = $email . "<td>" . $subitem->fromulocation . "</td>";
							$email = $email . "<td>" . $subitem->toareaname . "</td>";
							$email = $email . "<td>" . $subitem->tocabinet . "</td>";
							$email = $email . "<td>" . $subitem->topositionname . "</td>";
							$email = $email . "<td>" . $subitem->tolocationname . "</td>";
							$email = $email . "<td>" . $subitem->toulocation . "</td>";
							$email = $email . "</tr>";
						}
						
						$email = $email . "</table></td></tr>";
					}
					
					if ($item->longline != null && count($item->longline->item) > 0) {
						$email = $email . "<tr class='subtable'><td width='100%'colspan='5'><table cellpadding=0 cellspacing=0 width=100%>";
						$email = $email . "<thead style='font-style:italic; font-weight:bold'>";
						$email = $email . "<tr>";
						$email = $email . "<td>From Area</td>";
						$email = $email . "<td>From Cabinet</td>";
						$email = $email . "<td>To Area</td>";
						$email = $email . "<td>To Cabinet</td>";
						$email = $email . "<td>Notes</td>";
						$email = $email . "</tr>";
						$email = $email . "</thead>";
						
						for ($x = 0; $x < count($item->longline->item); $x++) {
							$subitem = $item->longline->item[$x];
							
							$email = $email . "<tr>";
							$email = $email . "<td>" . $subitem->fromareaname . "</td>";
							$email = $email . "<td>" . $subitem->fromcabinet . "</td>";
							$email = $email . "<td>" . $subitem->toareaname . "</td>";
							$email = $email . "<td>" . $subitem->tocabinet . "</td>";
							$email = $email . "<td>" . $subitem->notes . "</td>";
							$email = $email . "</tr>";
						}
						
						$email = $email . "</table></td></tr>";
					}
				}
			}
			
			$email = $email . "<tfoot>";
			$email = $email . "<tr>";
			$email = $email . "<td colspan=5 align=right>Grand total: <span id='grandtotal'>";
			$email = $email . number_format($grandTotal, 2);
			
			$email = $email . "</span></td>";
			$email = $email . "</tr>";
			$email = $email . "</tfoot>";
			$email = $email . "</table>";
			
			return $email;
	    }
	    
	    function getHeaderID() {
	    	if ($this->status == "N" || $this->status == "R") {
				return $this->prefix . sprintf("%04d", $this->headerid);

			} else {
				return $this->jobprefix . sprintf("%04d", $this->headerid);
			}
	    }
	    
	    function getStatus() {
	    	if ($this->status == "N") {
	    		if ($this->approvalid == 0) {
		    		return "Draft";
	    		}
	    		
	    		return "Awaiting Approval";
	    	}
	    	
	    	if ($this->status == "R") {
	    		return "Verification Rejected";
	    	}
	    	
	    	if ($this->status == "P") {
	    		return "Pending Cancellation";
	    	}
	    	
	    	if ($this->status == "A") {
	    		return "Verification Approved";
	    	}
	    	
	    	if ($this->status == "A") {
	    		return "Verification Approved";
	    	}
	    	
	    	if ($this->status == "S") {
	    		return "Scheduled";
	    	}
	    	
	    	if ($this->status == "I") {
	    		return "CE Approved";
	    	}
	    	
	    	if ($this->status == "Q") {
	    		return "Quality Assured";
	    	}
	    	
	    	if ($this->status == "C") {
	    		return "Completed";
	    	}
	    	
	    	if ($this->status == "V") {
	    		return "Handed Over";
	    	}
	    	
	    	if ($this->status == "X") {
	    		return "Cancelled";
	    	}
	    }
	    
	    function showHeaderDetails($history = true) {
	    	echo "<div id='historydialog' class='modal'>\n";
	    	echo "<table width='100%'>\n";
	    	echo "<tr>\n";
	    	echo "<td><label>Created By</label></td>\n";
	    	echo "<td>" . GetUserName( $this->createdby) . "</td>\n";
	    	echo "<td><label>At</label></td>\n";
	    	echo "<td>" . $this->createddate . "</td>\n";
	    	echo "</tr>\n";
	    	
	    	if ($this->approvalrequesteddate != null) {
	    		$first = true;
				$qry = "SELECT B.firstname, B.lastname " .
						"FROM jomon_userroles A " .
						"INNER JOIN jomon_members B " .
						"ON B.member_id = A.memberid " .
						"WHERE A.roleid = '$this->approvalid'";
				$result = mysql_query($qry);
				
				//Check whether the query was successful or not
				if($result) {
					while (($member = mysql_fetch_assoc($result))) {
				    	echo "<tr>\n";
				    	
						if ($first) {
							$first = false;
							
					    	echo "<td><label>Requested Approval From</label></td>\n";
					    	echo "<td>" . $member['firstname'] . " " . $member['lastname'] . "</td>\n";
					    	echo "<td><label>At</label></td>\n";
					    	echo "<td>" . $this->approvalrequesteddate . "</td>\n";
					    	
						} else {
					    	echo "<td>&nbsp;</td>\n";
					    	echo "<td>" . $member['firstname'] . " " . $member['lastname'] . "</td>\n";
					    	echo "<td>&nbsp;</td>\n";
					    	echo "<td>&nbsp;</td>\n";
						}
						
				    	echo "</tr>\n";
					}
					
				} else {
			    	echo "<tr>\n";
			    	echo "<td colspan=4><label>" . mysql_error() . "</label></td>\n";
			    	echo "</tr>\n";
				}
	    		
	    	}
	    	
	    	if ($this->approveddate != null) {
	    		if ($this->status == "R") {
			    	echo "<tr>\n";
			    	echo "<td><label>Rejected By</label></td>\n";
			    	echo "<td>" . GetUserName( $this->approvedby) . "</td>\n";
			    	echo "<td><label>At</label></td>\n";
			    	echo "<td>" . $this->approveddate . "</td>\n";
			    	echo "</tr>\n";
	    			
	    		} else {
			    	echo "<tr>\n";
			    	echo "<td><label>Verified By</label></td>\n";
			    	echo "<td>" . GetUserName( $this->approvedby) . "</td>\n";
			    	echo "<td><label>At</label></td>\n";
			    	echo "<td>" . $this->approveddate . "</td>\n";
			    	echo "</tr>\n";
	    		}
	    	}
	    	
	    	if ($this->scheduleddate != null) {
		    	echo "<tr>\n";
		    	echo "<td><label>Scheduled By</label></td>\n";
		    	echo "<td>" . GetUserName( $this->scheduledby) . "</td>\n";
		    	echo "<td><label>At</label></td>\n";
		    	echo "<td>" . $this->scheduleddate . "</td>\n";
		    	echo "</tr>\n";
	    	}
	    	
	    	if ($this->ceapproveddate != null) {
		    	echo "<tr>\n";
		    	echo "<td><label>CE Approved By</label></td>\n";
		    	echo "<td>" . GetUserName( $this->ceapprovedby) . "</td>\n";
		    	echo "<td><label>At</label></td>\n";
		    	echo "<td>" . $this->ceapproveddate . "</td>\n";
		    	echo "</tr>\n";
	    	}
	    	
	    	if ($this->completeddate != null) {
		    	echo "<tr>\n";
		    	echo "<td><label>Completed By</label></td>\n";
		    	echo "<td>" . GetUserName( $this->completedby) . "</td>\n";
		    	echo "<td><label>At</label></td>\n";
		    	echo "<td>" . $this->completeddate . "</td>\n";
		    	echo "</tr>\n";
	    	}
	    	
	    	if ($this->qadate != null) {
		    	echo "<tr>\n";
		    	echo "<td><label>QA'd By</label></td>\n";
		    	echo "<td>" . GetUserName( $this->qaby) . "</td>\n";
		    	echo "<td><label>At</label></td>\n";
		    	echo "<td>" . $this->qadate . "</td>\n";
		    	echo "</tr>\n";
	    	}
	    	
	    	if ($this->archiveddate != null) {
		    	echo "<tr>\n";
		    	echo "<td><label>Handed Over By</label></td>\n";
		    	echo "<td>" . GetUserName( $this->archivedby) . "</td>\n";
		    	echo "<td><label>At</label></td>\n";
		    	echo "<td>" . $this->archiveddate . "</td>\n";
		    	echo "</tr>\n";
	    	}
	    	
	    	if ($this->cancelleddate != null) {
		    	echo "<tr>\n";
		    	echo "<td><label>Cancelled By</label></td>\n";
		    	echo "<td>" . GetUserName( $this->cancelledby) . "</td>\n";
		    	echo "<td><label>At</label></td>\n";
		    	echo "<td>" . $this->cancelleddate . "</td>\n";
		    	echo "</tr>\n";
	    	}
	    	
	    	echo "</table>\n";
	    	echo "</div>\n";
	    	
	    	echo "<script>\n";
	    	echo "$(document).ready(\n";
			echo "function() {\n";
			echo "$('#historydialog').dialog({\n";
			echo "modal: true,\n";
			echo "autoOpen: false,\n";
			echo "width: 600,\n";
			echo "show:'fade',\n";
			echo "hide:'fade',\n";
			echo "title: 'Quote to Job history',\n";
			echo "buttons: {\n";
			echo "'Close': function() {\n";
			echo "$(this).dialog('close');\n";
			echo "}\n";
			echo "}\n";
			echo "});\n";
			echo "}\n";
			echo ");\n";
	    	echo "</script>";
	    	
	    	if (($this->status == "P" || $this->status == "X")) { 
		    	echo "<div id='cancelleddialog' class='modal'>\n";
		    	echo "<table width='100%'>\n";
		    	echo "<thead>\n";
		    	echo "<tr>\n";
		    	echo "<td>Role</td>\n";
		    	echo "<td>Action Taken</td>\n";
		    	echo "<td>Action By</td>\n";
		    	echo "<td>Status</td>\n";
		    	echo "</tr>\n";
		    	echo "</thead>\n";
		    	
				$qry = "SELECT DISTINCT B.roleid, B.status, " .
						"DATE_FORMAT(B.processeddate, '%d/%m/%Y %H:%i:%S') AS processeddate, D.firstname, D.lastname " .
						"FROM jomon_cancelledjobflowheader A " .
						"INNER JOIN jomon_cancelledjobflowdetail B  " .
						"ON B.flowheaderid = A.id  " .
						"INNER JOIN jomon_userroles C  " .
						"ON C.roleid = B.roleid  " .
						"LEFT OUTER JOIN jomon_members D  " .
						"ON D.member_id = B.processedby  " .
						"WHERE A.quoteid = $this->headerid ";
				$result = mysql_query($qry);
				
				//Check whether the query was successful or not
				if($result) {
					while (($member = mysql_fetch_assoc($result))) {
				    	echo "<tr>\n";
				    	echo "<td>" . $member['roleid'] . "</td>\n";
				    	echo "<td>" . $member['firstname'] . " " . $member['lastname'] . "</td>\n";
				    	echo "<td>" . $member['processeddate'] . "</td>\n";
				    	
				    	if ($member['status'] == "Y") {
					    	echo "<td><img src='images/approve.png' /></td>\n";
				    		
				    	} else if ($member['status'] == "N") {
					    	echo "<td><img src='images/delete.png' /></td>\n";
				    	}
				    	echo "</tr>\n";
					}
		    	}
		    	
		    	echo "</table>\n";
		    	echo "</div>\n";
		    	
		    	echo "<script>\n";
		    	echo "$(document).ready(\n";
				echo "function() {\n";
				echo "$('#cancelleddialog').dialog({\n";
				echo "modal: true,\n";
				echo "autoOpen: false,\n";
				echo "width: 600,\n";
				echo "show:'fade',\n";
				echo "hide:'fade',\n";
				echo "dialogClass: 'document-dialog ui-dialog-shadow',\n";
				echo "title: 'Cancellation Workflow',\n";
				echo "buttons: {\n";
				echo "'Close': function() {\n";
				echo "$(this).dialog('close');\n";
				echo "}\n";
				echo "}\n";
				echo "});\n";
				echo "}\n";
				echo ");\n";
		    	echo "</script>";
	    	}
	    	
			echo "<table class='headinggroup'>\n";
			echo "<tr>\n";
			
			if ($this->status == "N" || $this->status == "R") {
				echo "<td class='label'>Quote Number</td>\n";
				echo "<td>" . $this->prefix . sprintf("%04d", $this->headerid);

			} else {
				echo "<td class='label'>Job Number</td>\n";
				echo "<td>" . $this->jobprefix . sprintf("%04d", $this->headerid);
			}
			
			echo "&nbsp;<img title='Show History' class='historybutton' src='images/history.gif' onclick='$(\"#historydialog\").dialog(\"open\");' />";
			echo "</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td class='label'>Raised by</td>\n";
			echo "<td>" . GetUserName($this->createdby) . "</td>\n";
			echo "</tr>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td class='label'>Status</td>\n";
			echo "<td>" . $this->getStatus();

			if (($this->status == "P" || $this->status == "X")) {
				echo "&nbsp;<img title='Show Workflow' class='historybutton' src='images/workflow.png' onclick='$(\"#cancelleddialog\").dialog(\"open\");' />";
			}

			echo "</td>\n";
			echo "</tr>\n";
			
			if ($this->requiredbydate != "00/00/0000") {
				echo "<tr>\n";
				echo "<td class='label'>Required By</td>\n";
				echo "<td>" . $this->requiredbydate . "</td>\n";
				echo "</tr>\n";
			}
			
			echo "<tr>\n";
			echo "<td class='label'>Customer</td>\n";
			echo "<td>" . $this->customer . "</td>\n";
			echo "</tr>\n";
			
			if ($this->ccf != "") {
				echo "<td class='label'>CCF Number</td>\n";
				echo "<td>" . $this->ccf . "</td>\n";
				echo "</tr>\n";
			}
			
			if ($this->ccfpath != "") {
				echo "<td class='label'>CCF Path</td>\n";
				echo "<td>" . $this->ccfpath . "</td>\n";
				echo "</tr>\n";
			}
			
			if ($this->sungardpo != "") {
				echo "<td class='label'>Sungard PO</td>\n";
				echo "<td>" . $this->sungardpo . "</td>\n";
				echo "</tr>\n";
			}
			
			if ($this->customerpo != "") {
				echo "<td class='label'>Customer PO</td>\n";
				echo "<td>" . $this->customerpo . "</td>\n";
				echo "</tr>\n";
			}
			
			echo "<tr>\n";
			echo "<td class='label'>Contact</td>\n";
			echo "<td>" . GetUserName($this->contactid) . "</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td class='label'>Site</td>\n";
			echo "<td>" . GetSiteName($this->siteid) . "</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			
			if ($this->costcodedesc != "") {
				echo "<tr>\n";
				echo "<td class='label'>Cost Code</td>\n";
				echo "<td>" . $this->costcodedesc . "</td>\n";
				echo "</tr>\n";
			}
			
			if ($this->internalapprovalcode != "") {
				echo "<tr>\n";
				echo "<td class='label'>Internal Budget Code</td>\n";
				echo "<td>" . $this->internalapprovalcode . "</td>\n";
				echo "</tr>\n";
			}
			
			if ($this->requiredbydate != "00/00/0000") {
				echo "<tr>\n";
				echo "<td class='label'>CAB Install Date</td>\n";
				echo "<td>" . $this->cabinstalldate . "</td>\n";
				echo "</tr>\n";
			}

			if ($this->status == "R" || $this->status == "N") {
				echo "<tr>\n";
				echo "<td class='label'>Created By</td>\n";
				echo "<td>" . GetUserName($this->createdby) . "</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td class='label'>Created Date</td>\n";
				echo "<td>" . $this->createddate . "</td>\n";
				echo "</tr>\n";
			}
			
			if ($this->status == "A") {
				echo "<tr>\n";
				echo "<td class='label'>Verified By</td>\n";
				echo "<td>" . GetUserName($this->approvedby) . "</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td class='label'>Verified Date</td>\n";
				echo "<td>" . $this->approveddate . "</td>\n";
				echo "</tr>\n";
			}
			
			if ($this->status == "S") {
				echo "<tr>\n";
				echo "<td class='label'>Scheduled By</td>\n";
				echo "<td>" . GetUserName($this->scheduledby) . "</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td class='label'>Scheduled Date</td>\n";
				echo "<td>" . $this->scheduleddate . "</td>\n";
				echo "</tr>\n";
			}
			
			if ($this->status == "I") {
				echo "<tr>\n";
				echo "<td class='label'>CE Approved By</td>\n";
				echo "<td>" . GetUserName($this->ceapprovedby) . "</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td class='label'>CE Approved Date</td>\n";
				echo "<td>" . $this->ceapproveddate . "</td>\n";
				echo "</tr>\n";
			}
			
			if ($this->status == "C") {
				echo "<tr>\n";
				echo "<td class='label'>Completed By</td>\n";
				echo "<td>" . GetUserName($this->completedby) . "</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td class='label'>Completed Date</td>\n";
				echo "<td>" . $this->completeddate . "</td>\n";
				echo "</tr>\n";
			}
			
			if ($this->status == "Q") {
				echo "<tr>\n";
				echo "<td class='label'>QA By</td>\n";
				echo "<td>" . GetUserName($this->qaby) . "</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td class='label'>QA Date</td>\n";
				echo "<td>" . $this->qadate . "</td>\n";
				echo "</tr>\n";
			}
			
			if ($this->status == "V") {
				echo "<tr>\n";
				echo "<td class='label'>Handover By</td>\n";
				echo "<td>" . GetUserName($this->archivedby) . "</td>\n";
				echo "</tr>\n";
				echo "<tr>\n";
				echo "<td class='label'>Handover Date</td>\n";
				echo "<td>" . $this->archiveddate . "</td>\n";
				echo "</tr>\n";
			}

			echo "</table>\n";
	    }
	    
	    function showItemDetails() {
	    	echo "<div id='itemdetails'>\n";
	    	echo "<table  style='width:98%' class='grid' cellspacing=0 cellpadding=0>";
			echo "<thead>";
			echo "<tr>";
			echo "<td width='3%'></td>";
			echo "<td width='10%'>Qty</td>";
			echo "<td width='58%'>Description</td>";
			echo "<td width='15%' align=right style='padding-right:18px'>Price</td>";
			echo "<td width='15%' align=right style='padding-right:18px'>Total</td>";
			echo "</tr>";
			echo "</thead>";
			echo "</table>";
			
			echo "<div style='width:100%;height:190px; overflow-y: scroll'>";
			echo "<table style='width:98%' class='grid' cellspacing=0 cellpadding=0>";
			
			$grandTotal = 0;
			
			for ($i = 0; $i < $this->itemCount(); $i++) {
				$item = $this->get($i);
				
				if (! $item->deleted) {
					$grandTotal = $grandTotal + $item->total;
					
					echo "<tr>\n";
					echo "<td width='3%'>";

					if (($item->panel != null && count($item->panel->item) > 0) ||
					    ($item->longline != null && count($item->longline->item) > 0)) {
						echo "<img src='images/plus.gif' onclick='openClose(this)' />";
					}

					echo "</td>\n";
					echo "<td width='10%'>";
					echo number_format($item->qty, 2);
					echo "</td>\n";
					echo "<td width='58%'>" . $item->productdesc . "</td>\n";
					echo "<td width='15%' class='price' align=right>" . number_format($item->price, 2) . "</td>\n";
					echo "<td width='15%' class='total' align=right>" . number_format($item->total, 2) . "</td>\n";
					echo "</tr>\n";
					
					if ($item->panel != null && count($item->panel->item) > 0) {
						echo "<tr >\n";
						echo "<td width='100%' class='hiddensubtable' colspan=5>\n";
						echo "<span class='subtable'>\n";
						echo "<table cellpadding=0 cellspacing=0 width=100%>";
						echo "<thead>";
						echo "<tr>";
						echo "<td>From Area</td>";
						echo "<td>From Cabinet</td>";
						echo "<td>From Position</td>";
						echo "<td>From Location</td>";
						echo "<td>From Uloc</td>";
						echo "<td>To Area</td>";
						echo "<td>To Cabinet</td>";
						echo "<td>To Position</td>";
						echo "<td>To Location</td>";
						echo "<td>To Uloc</td>";
						echo "</tr>";
						echo "</thead>";
						
						for ($x = 0; $x < count($item->panel->item); $x++) {
							$subitem = $item->panel->item[$x];
							
							echo "<tr>";
							echo "<td>" . $subitem->fromareaname . "</td>";
							echo "<td>" . $subitem->fromcabinet . "</td>";
							echo "<td>" . $subitem->frompositionname . "</td>";
							echo "<td>" . $subitem->fromlocationname . "</td>";
							echo "<td>" . $subitem->fromulocation . "</td>";
							echo "<td>" . $subitem->toareaname . "</td>";
							echo "<td>" . $subitem->tocabinet . "</td>";
							echo "<td>" . $subitem->topositionname . "</td>";
							echo "<td>" . $subitem->tolocationname . "</td>";
							echo "<td>" . $subitem->toulocation . "</td>";
							echo "</tr>";
						}
						
						echo "</table></span></td></tr>";
					}
					
					if ($item->longline != null && count($item->longline->item) > 0) {
						echo "<tr  >\n";
						echo "<td width='100%' class='hiddensubtable' colspan='5'>";
						echo "<span class='subtable'>\n";
						echo "<table cellpadding=0 cellspacing=0 width='100%'>";
						echo "<thead>";
						echo "<tr>";
						echo "<td>From Area</td>";
						echo "<td>From Cabinet</td>";
						echo "<td>To Area</td>";
						echo "<td>To Cabinet</td>";
						echo "<td>Notes</td>";
						echo "</tr>";
						echo "</thead>";
						echo "<tbody>";
						
						for ($x = 0; $x < count($item->longline->item); $x++) {
							$subitem = $item->longline->item[$x];
							
							echo "<tr>";
							echo "<td>" . $subitem->fromareaname . "</td>";
							echo "<td>" . $subitem->fromcabinet . "</td>";
							echo "<td>" . $subitem->toareaname . "</td>";
							echo "<td>" . $subitem->tocabinet . "</td>";
							echo "<td>" . $subitem->notes . "</td>";
							echo "</tr>";
						}
						
						echo "</tbody>";
						echo "</table></span></td></tr>";
					}
				}
			}
			
			echo "</table>";
			echo "</div>";
			
			echo "<table style='width:98%' class='grid' cellspacing=0 cellpadding=0>";
			echo "<tfoot>";
			echo "<tr>";
			echo "<td colspan=5 align=right style='padding-right:18px'>Grand total: <span id='grandtotal'>";
			echo number_format($grandTotal, 2);
			
			echo "</span></td>";
			echo "</tr>";
			echo "</tfoot>";
			echo "</table>";
			echo "</div>";
	    }
	    
	    function getTotal() {
			$qry = "SELECT SUM(total) AS total " .
					"FROM jomon_quoteitem " .
					"WHERE headerid = $this->headerid";
			$result = mysql_query($qry);
			$ordervalue = 0;
			
			//Check whether the query was successful or not
			if($result) {
				while (($member = mysql_fetch_assoc($result))) {
					$ordervalue = $member['total'];
				}
			}
			
			return $ordervalue;
	    }
	    
		function approve() {
			$this->status = "A";
			$this->notes = dateStampString($this->oldnotes, $this->notes, "VERIFICATION NOTES");
			
			$qry = "UPDATE jomon_quoteheader SET " .
					"status = 'A', " .
					"notes = '$this->notes', " .
					"approveddate = NOW(), " .
					"approvedby = " . $_SESSION['SESS_MEMBER_ID'] . " " .
					"WHERE id = $this->headerid";
			$result = mysql_query($qry);
			
		   	if (! $result) {
		   		die("Error UPDATE jomon_quoteheader (APPROVE):" . $qry . " - " . mysql_error());
		   	}
			
			$ordervalue = $this->getTotal();

			if ($this->costcode == "CAPEXCCF") {
				$qry = "UPDATE jomon_siteconfig SET currentcapexdealpovalue = currentcapexdealpovalue - $ordervalue";
				$result = mysql_query($qry);
				
			   	if (! $result) {
			   		die("Error UPDATE jomon_siteconfig (APPROVE):" . $qry . " - " . mysql_error());
			   	}
			
				$qry = "SELECT currentcapexdealpovalue, capexdealpovalue, capexdealpovaluethreshold " .
						"FROM jomon_siteconfig";
				$result = mysql_query($qry);
				
				//Check whether the query was successful or not
				if($result) {
					while (($member = mysql_fetch_assoc($result))) {
						$capexdealpovalue = $member['capexdealpovalue'];
						$currentcapexdealpovalue = $member['currentcapexdealpovalue'];
						$capexdealpovaluethreshold = $member['capexdealpovaluethreshold'];
						
						if ((($capexdealpovaluethreshold / 100) * $capexdealpovalue) > $currentcapexdealpovalue) {
							sendRoleMessage(
									'POMANAGER',
									"Threshold exceeded",
									"<h1>Quotation " . $this->prefix . sprintf("%04d", $this->headerid) . " has exceeded the CAPEX DEAL budget.</h1>" . $this->getEmailDetails()
								);
						}
 					}
				}
			}
			
			$this->load($this->headerid);
			
			$qry = "INSERT INTO jomon_jobheader (prefix, quoteid) VALUES ('SC-194SG', $this->headerid)";
			$result = mysql_query($qry);
			
		   	if (! $result) {
		   		die("Error INSERT jomon_jobheader (APPROVE):" . $qry . " - " . mysql_error());
		   	}

			sendUserMessage(
					$this->contactid,
					"Quotation Verified",
					"<h1>Quotation " . $this->prefix . sprintf("%04d", $this->headerid) . " has been verified by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
				
			sendUserMessage(
					$this->createdby,
					"Quotation Verified",
					"<h1>Quotation " . $this->prefix . sprintf("%04d", $this->headerid) . " has been verified by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
				
			sendRoleMessage(
					$this->approvalid,
					"Quotation Verified",
					"<h1>Quotation " . $this->prefix . sprintf("%04d", $this->headerid) . " has been verified by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);

			sendRoleMessage(
					'CEAPPROVAL',
					"Quotation Verified",
					"<h1>Quotation " . $this->prefix . sprintf("%04d", $this->headerid) . " has been verified by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);

			sendRoleMessage(
					'SCHEDULE',
					"Quotation Verified",
					"<h1>Quotation " . $this->prefix . sprintf("%04d", $this->headerid) . " has been verified by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);

			sendRoleMessage(
					'FINANCE',
					"Quotation Verified",
					"<h1>Quotation " . $this->prefix . sprintf("%04d", $this->headerid) . " has been verified by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
		}
		
		function reject() {
			$this->status = "R";
			$this->notes = dateStampString($this->oldnotes, $this->notes, "VERIFICATION REJECTION NOTES");
			
			$qry = "UPDATE jomon_quoteheader SET " .
					"status = 'R', " .
					"notes = '$this->notes', " .
					"approvalid = null, " .
					"approveddate = NOW(), " .
					"approvedby = " . $_SESSION['SESS_MEMBER_ID'] . " " .
					"WHERE id = $this->headerid";
			$result = mysql_query($qry);
			
			$this->load($this->headerid);
				
			sendUserMessage(
					$this->contactid,
					"Quotation Rejected",
					"<h1>Quotation " . $this->prefix . sprintf("%04d", $this->headerid) . " has been rejected by " . GetUserName() . "</h1><h2>Reason</h2><p>" . $this->notes . "</p>" . $this->getEmailDetails()
				);
				
			sendUserMessage(
					$this->createdby,
					"Quotation Rejected",
					"<h1>Quotation " . $this->prefix . sprintf("%04d", $this->headerid) . " has been rejected by " . GetUserName() . "</h1><h2>Reason</h2><p>" . $this->notes . "</p>" . $this->getEmailDetails()
				);
				
			sendRoleMessage(
					$this->approvalid,
					"Quotation Rejected",
					"<h1>Quotation " . $this->prefix . sprintf("%04d", $this->headerid) . " has been rejected by " . GetUserName() . "</h1><h2>Reason</h2><p>" . $this->notes . "</p>" . $this->getEmailDetails()
				);
		}
	    
		public function schedule() {
			$mysql_scheduledate = substr($this->scheduledate, 6, 4 ) . "-" . substr($this->scheduledate, 3, 2 ) . "-" . substr($this->scheduledate, 0, 2 ) . " " . substr($this->scheduledate, 11, 5 );
			$this->status = "S";
			$this->notes = dateStampString($this->oldnotes, $this->notes, "SCHEDULE NOTES");
			
			$qry = "UPDATE jomon_quoteheader SET " .
					"status = 'S', " .
					"notes = '$this->notes', " .
					"scheduleddate = '$mysql_scheduledate', " .
					"scheduledby = " . $_SESSION['SESS_MEMBER_ID'] . " " .
					"WHERE id = $this->headerid";
			$result = mysql_query($qry);
			
			$this->load($this->headerid);
				
			sendUserMessage(
					$this->contactid,
					"Job Scheduled",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has been scheduled by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
				
			sendUserMessage(
					$this->createdby,
					"Job Scheduled",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has been scheduled by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
				
			sendRoleMessage(
					'SCHEDULE',
					"Job Scheduled",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has been scheduled by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
				
			sendRoleMessage(
					'CEAPPROVAL',
					"Job Scheduled",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has been scheduled by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
		}
	    
		public function implement() {
			$this->status = "I";
			$this->notes = dateStampString($this->oldnotes, $this->notes, "CE APPROVAL NOTES");
			
			$qry = "UPDATE jomon_quoteheader SET " .
					"status = 'I', " .
					"notes = '$this->notes', " .
					"ceapproveddate = NOW(), " .
					"ceapprovedby = " . $_SESSION['SESS_MEMBER_ID'] . " " .
					"WHERE id = $this->headerid";
			$result = mysql_query($qry);
			
			$this->load($this->headerid);
				
			sendUserMessage(
					$this->contactid,
					"Job CE Approved",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has been CE approved by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
				
			sendUserMessage(
					$this->createdby,
					"Job CE Approved",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has been CE approved by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
				
			sendRoleMessage(
					'CEAPPROVAL',
					"Job CE Approved",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has been CE approved by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
				
			sendRoleMessage(
					'COMPLETE',
					"Job CE Approved",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has been CE approved by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
		}
	    
		public function implementreject() {
			$this->notes = dateStampString($this->oldnotes, $this->notes, "CE REJECTION NOTES");
			
			$qry = "UPDATE jomon_quoteheader SET " .
					"notes = '$this->notes', " .
					"status = 'A' " .
					"WHERE id = $this->headerid";
			$result = mysql_query($qry);
			
		   	if (! $result) {
		   		die("Error UPDATE jomon_quoteheader (COMPLETE):" . $qry . " - " . mysql_error());
		   	}
			
			$this->load($this->headerid);
		   	
			sendUserMessage(
					$this->contactid,
					"Job CE Rejected",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has been CE rejected by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
				
			sendUserMessage(
					$this->createdby,
					"Job CE Rejected",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has been CE rejected by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
				
			sendRoleMessage(
					'CEAPPROVAL',
					"Job CE Rejected",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has been CE rejected by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
				
			sendRoleMessage(
					'SCHEDULE',
					"Job CE Rejected",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has been CE rejected by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
		}
		
		public function saveNotes() {
			$this->notes = dateStampString($this->oldnotes, $this->notes, "ADDITIONAL NOTES");
			
			$qry = "UPDATE jomon_quoteheader SET " .
					"notes = '$this->notes' " .
					"WHERE id = $this->headerid";
			$result = mysql_query($qry);
		}
		
		public function deleteThrowAwayQuote() {
			$qry = "DELETE FROM jomon_cancelledquoteitem " .
					"WHERE headerid = $this->headerid";
			$result = mysql_query($qry);
			
		   	if (! $result) {
		   		die("DELETE FROM jomon_cancelledquoteitem:" . $qry . " - " . mysql_error());
		   	}
			
			$qry = "DELETE FROM jomon_cancelledquoteheader " .
					"WHERE id = $this->headerid";
			$result = mysql_query($qry);
			
		   	if (! $result) {
		   		die("DELETE FROM jomon_cancelledquoteheader:" . $qry . " - " . mysql_error());
		   	}
			
			sendUserMessage(
					$this->createdby,
					"Throw Away Quote Deleted",
					"<h1>Job " . $this->prefix . sprintf("%04d", $this->headerid) . " has been deleteed by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
		}
		
		public function saveThrowAwayNotes() {
			$this->notes = dateStampString($this->oldnotes, $this->notes, "ADDITIONAL NOTES");
			
			$qry = "UPDATE jomon_cancelledquoteheader SET " .
					"notes = '$this->notes' " .
					"WHERE id = $this->headerid";
			$result = mysql_query($qry);
		}
	    
		public function completeQuote() {
			$this->status = "C";
			$this->notes = dateStampString($this->oldnotes, $this->notes, "COMPLETION NOTES");
			
			$qry = "UPDATE jomon_quoteheader SET " .
					"status = 'C', " .
					"notes = '$this->notes', " .
					"completeddate = NOW(), " .
					"completedby = " . $_SESSION['SESS_MEMBER_ID'] . " " .
					"WHERE id = $this->headerid";
			$result = mysql_query($qry);
			
		   	if (! $result) {
		   		die("Error UPDATE jomon_quoteheader (COMPLETE):" . $qry . " - " . mysql_error());
		   	}
			
			$this->load($this->headerid);
				
			sendUserMessage(
					$this->contactid,
					"Job Completed",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has been completed by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
				
			sendUserMessage(
					$this->createdby,
					"Job Completed",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has been completed by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);

			sendRoleMessage(
					'COMPLETE',
					"Job Completed",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has been completed by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);

			sendRoleMessage(
					'QA',
					"Job Completed",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has been completed by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
		}
	    
		public function passQA() {
			$this->status = "Q";
			$this->notes = dateStampString($this->oldnotes, $this->notes, "QA SUCCESS NOTES");
			
			$qry = "UPDATE jomon_quoteheader SET " .
					"status = 'Q', " .
					"notes = '$this->notes', " .
					"qadate = NOW(), " .
					"qaby = " . $_SESSION['SESS_MEMBER_ID'] . " " .
					"WHERE id = $this->headerid";
			$result = mysql_query($qry);
			
			$this->load($this->headerid);
				
			sendUserMessage(
					$this->contactid,
					"Job passed QA",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has passed QA by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
				
			sendUserMessage(
					$this->createdby,
					"Job passed QA",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has passed QA by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);

			sendRoleMessage(
					'QA',
					"Job passed QA",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has passed QA by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);

			sendRoleMessage(
					'ARCHIVE',
					"Job passed QA",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has passed QA by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
		}
	    
		public function rejectQA() {
			$this->notes = dateStampString($this->oldnotes, $this->notes, "QA REJECTION NOTES");
			
			$qry = "UPDATE jomon_quoteheader SET " .
					"notes = '$this->notes'," .
					"status = 'I' " .
					"WHERE id = $this->headerid";
			$result = mysql_query($qry);
			
			$this->load($this->headerid);
				
			sendUserMessage(
					$this->contactid,
					"Job rejected QA",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has been rejected by QA / " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
				
			sendUserMessage(
					$this->createdby,
					"Job rejected QA",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has been rejected by QA / " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);

			sendRoleMessage(
					'QA',
					"Job rejected QA",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has been rejected by QA / " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);

			sendRoleMessage(
					'COMPLETE',
					"Job rejected QA",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " has been rejected by QA / " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
		}
	    
		public function archive() {
			$this->status = "V";
			$this->notes = dateStampString($this->oldnotes, $this->notes, "HANDOVER NOTES");
			
			$qry = "UPDATE jomon_quoteheader SET " .
					"status = 'V', " .
					"notes = '$this->notes', " .
					"archiveddate = NOW(), " .
					"archivedby = " . $_SESSION['SESS_MEMBER_ID'] . " " .
					"WHERE id = $this->headerid";
			$result = mysql_query($qry);
			
		   	if (! $result) {
		   		die("Error UPDATE jomon_quoteheader (ARCHIVE):" . $qry . " - " . mysql_error());
		   	}
			
			$this->load($this->headerid);
				
			sendUserMessage(
					$this->contactid,
					"Job handed over",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " handed over by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
				
			sendUserMessage(
					$this->createdby,
					"Job handed over",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " handed over by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);

			sendRoleMessage(
					'ARCHIVE',
					"Job handed over",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " handed over by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);

			sendRoleMessage(
					'FINANCE',
					"Job handed over",
					"<h1>Job " . $this->jobprefix . sprintf("%04d", $this->headerid) . " handed over by " . GetUserName() . "</h1>" . $this->getEmailDetails()
				);
		}
	    
	    public function requestApproval() {
	    	$below1kid = "";
	    	$below5kid = "";
	    	$over5kid = "";
		    	
			$qry = "SELECT * FROM jomon_siteconfig";
			$result = mysql_query($qry);
			
			//Check whether the query was successful or not
			if($result) {
				while (($member = mysql_fetch_assoc($result))) {
					$below1kid = $member['below1kid'];
					$below5kid = $member['below5kid'];
					$over5kid = $member['over5kid'];
				}
			}
						
			$qry = "SELECT A.prefix, A.status, A.notes, A.id, A.ccf, B.login, A.customer AS clientname, " .
					"D.name AS sitename, SUM(E.total) AS ordervalue " .
					"FROM jomon_quoteheader A " .
					"INNER JOIN jomon_members B " .
					"ON B.member_id = A.createdby " .
					"INNER JOIN jomon_sites D " .
					"ON D.id = A.siteid " .
					"INNER JOIN jomon_quoteitem E " .
					"ON E.headerid = A.id " .
					"WHERE A.id = $this->headerid " .
					"GROUP BY A.prefix, A.status, A.notes, A.id, A.ccf, B.login, A.customer, D.name ";
			$result = mysql_query($qry);
			$body = "";
		
			//Check whether the query was successful or not
			if($result) {
				while (($member = mysql_fetch_assoc($result))) {
					$status = $member['status'];
					$url = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
					$app = substr($url, lastIndexOf($url, "/") + 1);
					$url = str_replace($app, "approval.php?id=" . $this->headerid, $url);
					
					$body =
						"<h2>Approval required for the following quotation.</h2>" .
						$this->getEmailDetails() .
						"<a href='$url'>Please Click here to Approve or Reject this quotation</a><br>";
						
					if ($member['ordervalue'] < 1000) {
						$approvalid = $below1kid;
							
					} else if ($member['ordervalue'] < 5000) {
						$approvalid = $below5kid;
							
					} else {
						$approvalid = $over5kid;
					}
					
					sendRoleMessage(
							$approvalid,
							"Quotation Approval", 
							$body
						);
					
					$qry = "UPDATE jomon_quoteheader SET " .
							"approvalid = '$approvalid', " .
							"approvalrequesteddate = NOW() " .
							"WHERE id = $this->headerid";
					 mysql_query($qry);
				}
			}
	    }
	    
	    public function loadThrowAway($headerid) {
			$qry = "SELECT A.siteid, A.prefix, A.status, A.contactid, A.costcode, A.customer, A.ccf, A.customerpo, " .
					"DATE_FORMAT(A.cabinstalldate, '%d/%m/%Y') AS cabinstalldate, " .
					"DATE_FORMAT(A.requiredby, '%d/%m/%Y') AS requiredby, " .
					"DATE_FORMAT(A.qadate, '%d/%m/%Y %H:%i') AS qadate, " .
					"DATE_FORMAT(A.ceapproveddate, '%d/%m/%Y %H:%i') AS ceapproveddate, " .
					"DATE_FORMAT(A.approveddate, '%d/%m/%Y %H:%i') AS approveddate, " .
					"DATE_FORMAT(A.createddate, '%d/%m/%Y %H:%i') AS createddate, " .
					"DATE_FORMAT(A.scheduleddate, '%d/%m/%Y %H:%i') AS scheduleddate, " .
					"DATE_FORMAT(A.completeddate, '%d/%m/%Y %H:%i') AS completeddate, " .
					"DATE_FORMAT(A.archiveddate, '%d/%m/%Y %H:%i') AS archiveddate, " .
					"A.ceapprovedby, A.notes, A.id, A.createdby, A.approvalid, A.approvedby, " .
					"A.scheduledby, A.completedby, A.archivedby, A.qaby, A.ccfpath, A.sungardpo, " .
					"A.internalapprovalcode, B.id AS jobid, B.prefix AS jobprefix " .
					"FROM jomon_cancelledquoteheader A " .
					"LEFT OUTER JOIN jomon_jobheader B " .
					"ON B.quoteid = A.id " .
					"WHERE A.id = $headerid";
			$result = mysql_query($qry);
			   	
		   	if (! $result) {
		   		die("Error SELECT FROM jomon_cancelledquoteheader:" . $qry . " - " . mysql_error());
		   	}
			
			//Check whether the query was successful or not
			while (($member = mysql_fetch_assoc($result))) {
			    $this->siteid = $member['siteid'];
			    $this->status = $member['status'];
			    $this->prefix = $member['prefix'];
			    $this->contactid = $member['contactid'];
			    $this->costcode = $member['costcode'];
			    $this->oldcostcode = $member['costcode'];
			    $this->customer = $member['customer'];
			    $this->jobid = $member['jobid'];
			    $this->jobprefix = $member['jobprefix'];
			    $this->ccf = $member['ccf'];
			    $this->internalapprovalcode = $member['internalapprovalcode'];
			    $this->customerpo = $member['customerpo'];
			    $this->cabinstalldate = $member['cabinstalldate'];
			    $this->requiredbydate = $member['requiredby'];
			    $this->sungardpo = $member['sungardpo'];
			    $this->ccfpath = $member['ccfpath'];
			    $this->notes = $member['notes'];
			    $this->oldnotes = $member['notes'];
			    $this->headerid = $member['id'];
			    $this->ceapprovedby = $member['ceapprovedby'];
			    $this->ceapproveddate = $member['ceapproveddate'];
			    $this->createdby = $member['createdby'];
			    $this->createddate = $member['createddate'];
			    $this->approvalid = $member['approvalid'];
			    $this->approvedby = $member['approvedby'];
			    $this->approveddate = $member['approveddate'];
			    $this->scheduledby = $member['scheduledby'];
			    $this->scheduleddate = $member['scheduleddate'];
			    $this->archivedby = $member['archivedby'];
			    $this->archiveddate = $member['archiveddate'];
			    $this->completedby = $member['completedby'];
			    $this->completeddate = $member['completeddate'];
			    $this->qaby = $member['qaby'];
			    $this->qadate = $member['qadate'];
				$this->items = array();
				$this->costcodedesc = GetCostCode($this->costcode);
			    
			    $index = 0;
				$qry = "SELECT * FROM jomon_cancelledquoteitem " .
						"WHERE headerid = $headerid";
				$itemresult = mysql_query($qry);
				
				//Check whether the query was successful or not
				if($itemresult) {
					while (($itemmember = mysql_fetch_assoc($itemresult))) {
						$quoteitem = new QuotationItem();
						$quoteitem->productdesc = $itemmember['description'];
						$quoteitem->notes = $itemmember['notes'];
						$quoteitem->qty = $itemmember['qty'];
						$quoteitem->price = $itemmember['price'];
						$quoteitem->total = $itemmember['total'];
						$quoteitem->id = $itemmember['id'];
						$quoteitem->itemnumber = $itemmember['linenumber'];
						$quoteitem->productid = $itemmember['productid'];
						$quoteitem->productlengthid = $itemmember['lengthid'];
						$quoteitem->cat1 = $itemmember['cat1'];
						$quoteitem->cat2 = $itemmember['cat2'];
						$quoteitem->cat3 = $itemmember['cat3'];
						$quoteitem->manneddays = $itemmember['manneddays'];
						
						$this->items[$index] = $quoteitem;
						
						$index++;

						$qry = "SELECT A.*, B.name AS fromareaname, C.name AS toareaname " .
								"FROM jomon_cancelledquoteitemlongline A " .
								"INNER JOIN jomon_areas B " .
								"ON B.id = A.fromareaid " .
								"INNER JOIN jomon_areas C " .
								"ON C.id = A.toareaid " .
							    "WHERE A.quoteitemid = " . $itemmember['id'];
						$subitemresult = mysql_query($qry);
						$subitemindex = 0;
				
						if($subitemresult) {
							$quoteitem->longline = new LongLineItem();
							
							while (($subitemmember = mysql_fetch_assoc($subitemresult))) {
								$subitem = new LongLineSubItem();
								$subitem->fromarea = $subitemmember['fromareaid'];
								$subitem->fromareaname = $subitemmember['fromareaname'];
								$subitem->fromcabinet = $subitemmember['fromcabinet'];
								$subitem->toarea = $subitemmember['toareaid'];
								$subitem->toareaname = $subitemmember['toareaname'];
								$subitem->tocabinet = $subitemmember['tocabinet'];
								$subitem->notes = $subitemmember['notes'];
								
								$quoteitem->longline->item[$subitemindex] = $subitem;
								
								$subitemindex++;
							}
						}

						$qry = "SELECT A.*, B.name AS fromareaname, C.name AS toareaname " .
								"FROM jomon_cancelledquoteitempanel A " .
								"INNER JOIN jomon_areas B " .
								"ON B.id = A.fromareaid " .
								"INNER JOIN jomon_areas C " .
								"ON C.id = A.toareaid " .
							   "WHERE A.quoteitemid = " . $itemmember['id'];
						$subitemresult = mysql_query($qry);
						$subitemindex = 0;
				
						if($subitemresult) {
							$quoteitem->panel = new PanelItem();
							
							while (($subitemmember = mysql_fetch_assoc($subitemresult))) {
								$subitem = new PanelSubItem();
								$subitem->fromarea = $subitemmember['fromareaid'];
								$subitem->fromareaname = $subitemmember['fromareaname'];
								$subitem->fromcabinet = $subitemmember['fromcabinet'];
								$subitem->fromposition = $subitemmember['fromposition'];
								$subitem->fromlocation = $subitemmember['fromlocation'];
								$subitem->fromulocation = $subitemmember['fromuloc'];
								$subitem->toarea = $subitemmember['toareaid'];
								$subitem->toareaname = $subitemmember['toareaname'];
								$subitem->tocabinet = $subitemmember['tocabinet'];
								$subitem->toposition = $subitemmember['toposition'];
								$subitem->tolocation = $subitemmember['tolocation'];
								$subitem->toulocation = $subitemmember['touloc'];
								
								if ($subitem->fromposition == "R") {
									$subitem->frompositionname = "Rear";
									
								} else if ($subitem->fromposition == "F") {
									$subitem->frompositionname = "Front";
								}
								
								if ($subitem->toposition == "R") {
									$subitem->topositionname = "Rear";
									
								} else if ($subitem->toposition == "F") {
									$subitem->topositionname = "Front";
								}
								
								if ($subitem->fromlocation == "T") {
									$subitem->fromlocationname = "Next Available 'U' From Top";
									
								} else if ($subitem->fromlocation == "B") {
									$subitem->fromlocationname = "Next Available 'U' From Bottom";
									
								} else if ($subitem->fromlocation == "U") {
									$subitem->fromlocationname = "'U' Location";
								}
								
								if ($subitem->tolocation == "T") {
									$subitem->tolocationname = "Next Available 'U' From Top";
									
								} else if ($subitem->tolocation == "B") {
									$subitem->tolocationname = "Next Available 'U' From Bottom";
									
								} else if ($subitem->tolocation == "U") {
									$subitem->tolocationname = "'U' Location";
								}
								
								$quoteitem->panel->item[$subitemindex] = $subitem;
								
								$subitemindex++;
							}
						}
					}
				}
			}
			
	    	$_SESSION['QUOTATION'] = $this;
	    }
	    
	    public function load($headerid) {
			$qry = "SELECT A.siteid, A.prefix, A.status, A.contactid, A.costcode, A.customer, A.ccf, A.customerpo, " .
					"DATE_FORMAT(A.cabinstalldate, '%d/%m/%Y') AS cabinstalldate, " .
					"DATE_FORMAT(A.requiredby, '%d/%m/%Y') AS requiredby, " .
					"DATE_FORMAT(A.qadate, '%d/%m/%Y %H:%i') AS qadate, " .
					"DATE_FORMAT(A.ceapproveddate, '%d/%m/%Y %H:%i') AS ceapproveddate, " .
					"DATE_FORMAT(A.approveddate, '%d/%m/%Y %H:%i') AS approveddate, " .
					"DATE_FORMAT(A.createddate, '%d/%m/%Y %H:%i') AS createddate, " .
					"DATE_FORMAT(A.scheduleddate, '%d/%m/%Y %H:%i') AS scheduleddate, " .
					"DATE_FORMAT(A.completeddate, '%d/%m/%Y %H:%i') AS completeddate, " .
					"DATE_FORMAT(A.archiveddate, '%d/%m/%Y %H:%i') AS archiveddate, " .
					"DATE_FORMAT(A.cancelleddate, '%d/%m/%Y %H:%i') AS cancelleddate, " .
					"DATE_FORMAT(A.approvalrequesteddate, '%d/%m/%Y %H:%i') AS approvalrequesteddate, " .
					"A.cancelledby, A.ceapprovedby, A.notes, A.id, A.createdby, A.approvalid, A.approvedby, " .
					"A.scheduledby, A.completedby, A.archivedby, A.qaby, A.ccfpath, A.sungardpo, A.originalstatus, " .
					"A.internalapprovalcode, B.id AS jobid, B.prefix AS jobprefix " .
					"FROM jomon_quoteheader A " .
					"LEFT OUTER JOIN jomon_jobheader B " .
					"ON B.quoteid = A.id " .
					"WHERE A.id = $headerid";
			$result = mysql_query($qry);
			   	
		   	if (! $result) {
		   		die("Error SELECT FROM jomon_quoteheader:" . $qry . " - " . mysql_error());
		   	}
			
			//Check whether the query was successful or not
			while (($member = mysql_fetch_assoc($result))) {
			    $this->siteid = $member['siteid'];
			    $this->status = $member['status'];
			    $this->originalstatus = $member['originalstatus'];
			    $this->prefix = $member['prefix'];
			    $this->contactid = $member['contactid'];
			    $this->costcode = $member['costcode'];
			    $this->oldcostcode = $member['costcode'];
			    $this->customer = $member['customer'];
			    $this->jobid = $member['jobid'];
			    $this->jobprefix = $member['jobprefix'];
			    $this->ccf = $member['ccf'];
			    $this->internalapprovalcode = $member['internalapprovalcode'];
			    $this->customerpo = $member['customerpo'];
			    $this->cabinstalldate = $member['cabinstalldate'];
			    $this->requiredbydate = $member['requiredby'];
			    $this->sungardpo = $member['sungardpo'];
			    $this->ccfpath = $member['ccfpath'];
			    $this->notes = $member['notes'];
			    $this->oldnotes = $member['notes'];
			    $this->headerid = $member['id'];
			    $this->cancelledby = $member['cancelledby'];
			    $this->cancelleddate = $member['cancelleddate'];
			    $this->ceapprovedby = $member['ceapprovedby'];
			    $this->ceapproveddate = $member['ceapproveddate'];
			    $this->createdby = $member['createdby'];
			    $this->createddate = $member['createddate'];
			    $this->approvalid = $member['approvalid'];
			    $this->approvalrequesteddate = $member['approvalrequesteddate'];
			    $this->approvedby = $member['approvedby'];
			    $this->approveddate = $member['approveddate'];
			    $this->scheduledby = $member['scheduledby'];
			    $this->scheduleddate = $member['scheduleddate'];
			    $this->archivedby = $member['archivedby'];
			    $this->archiveddate = $member['archiveddate'];
			    $this->completedby = $member['completedby'];
			    $this->completeddate = $member['completeddate'];
			    $this->qaby = $member['qaby'];
			    $this->qadate = $member['qadate'];
				$this->items = array();
				
				if ($this->costcode == "CAPEXCCF") {
					$this->costcodedesc = "CAPEX DEAL RELATED CCF";
						
				} else if ($this->costcode == "CAPEXINTERNAL") {
					$this->costcodedesc = "CAPEX NON DEAL RELATED";	
					
				} else if ($this->costcode == "OPEXINTERNAL") {
					$this->costcodedesc = "OPEX NON DEAL RELATED";	
						
				} else if ($this->costcode == "CAPEXBESPOKE") {
					$this->costcodedesc = "CAPEX - BESPOKE";	
						
				} else if ($this->costcode == "OPEXBESPOKE") {
					$this->costcodedesc = "OPEX - BESPOKE";	
						
				} else if ($this->costcode == "OPEXCUSTOMERPO") {
					$this->costcodedesc = "OPEX - Customer PO";	
				}
			    
			    $index = 0;
				$qry = "SELECT * FROM jomon_quoteitem " .
						"WHERE headerid = $headerid";
				$itemresult = mysql_query($qry);
				
				//Check whether the query was successful or not
				if($itemresult) {
					while (($itemmember = mysql_fetch_assoc($itemresult))) {
						$quoteitem = new QuotationItem();
						$quoteitem->productdesc = $itemmember['description'];
						$quoteitem->notes = $itemmember['notes'];
						$quoteitem->qty = $itemmember['qty'];
						$quoteitem->price = $itemmember['price'];
						$quoteitem->total = $itemmember['total'];
						$quoteitem->id = $itemmember['id'];
						$quoteitem->itemnumber = $itemmember['linenumber'];
						$quoteitem->productid = $itemmember['productid'];
						$quoteitem->productlengthid = $itemmember['lengthid'];
						$quoteitem->cat1 = $itemmember['cat1'];
						$quoteitem->cat2 = $itemmember['cat2'];
						$quoteitem->cat3 = $itemmember['cat3'];
						$quoteitem->manneddays = $itemmember['manneddays'];
						
						$this->items[$index] = $quoteitem;
						
						$index++;

						$qry = "SELECT A.*, B.name AS fromareaname, C.name AS toareaname " .
								"FROM jomon_quoteitemlongline A " .
								"INNER JOIN jomon_areas B " .
								"ON B.id = A.fromareaid " .
								"INNER JOIN jomon_areas C " .
								"ON C.id = A.toareaid " .
							    "WHERE A.quoteitemid = " . $itemmember['id'];
						$subitemresult = mysql_query($qry);
						$subitemindex = 0;
				
						if($subitemresult) {
							$quoteitem->longline = new LongLineItem();
							
							while (($subitemmember = mysql_fetch_assoc($subitemresult))) {
								$subitem = new LongLineSubItem();
								$subitem->fromarea = $subitemmember['fromareaid'];
								$subitem->fromareaname = $subitemmember['fromareaname'];
								$subitem->fromcabinet = $subitemmember['fromcabinet'];
								$subitem->toarea = $subitemmember['toareaid'];
								$subitem->toareaname = $subitemmember['toareaname'];
								$subitem->tocabinet = $subitemmember['tocabinet'];
								$subitem->notes = $subitemmember['notes'];
								
								$quoteitem->longline->item[$subitemindex] = $subitem;
								
								$subitemindex++;
							}
						}

						$qry = "SELECT A.*, B.name AS fromareaname, C.name AS toareaname " .
								"FROM jomon_quoteitempanel A " .
								"INNER JOIN jomon_areas B " .
								"ON B.id = A.fromareaid " .
								"INNER JOIN jomon_areas C " .
								"ON C.id = A.toareaid " .
							   "WHERE A.quoteitemid = " . $itemmember['id'];
						$subitemresult = mysql_query($qry);
						$subitemindex = 0;
				
						if($subitemresult) {
							$quoteitem->panel = new PanelItem();
							
							while (($subitemmember = mysql_fetch_assoc($subitemresult))) {
								$subitem = new PanelSubItem();
								$subitem->fromarea = $subitemmember['fromareaid'];
								$subitem->fromareaname = $subitemmember['fromareaname'];
								$subitem->fromcabinet = $subitemmember['fromcabinet'];
								$subitem->fromposition = $subitemmember['fromposition'];
								$subitem->fromlocation = $subitemmember['fromlocation'];
								$subitem->fromulocation = $subitemmember['fromuloc'];
								$subitem->toarea = $subitemmember['toareaid'];
								$subitem->toareaname = $subitemmember['toareaname'];
								$subitem->tocabinet = $subitemmember['tocabinet'];
								$subitem->toposition = $subitemmember['toposition'];
								$subitem->tolocation = $subitemmember['tolocation'];
								$subitem->toulocation = $subitemmember['touloc'];
								
								if ($subitem->fromposition == "R") {
									$subitem->frompositionname = "Rear";
									
								} else if ($subitem->fromposition == "F") {
									$subitem->frompositionname = "Front";
								}
								
								if ($subitem->toposition == "R") {
									$subitem->topositionname = "Rear";
									
								} else if ($subitem->toposition == "F") {
									$subitem->topositionname = "Front";
								}
								
								if ($subitem->fromlocation == "T") {
									$subitem->fromlocationname = "Next Available 'U' From Top";
									
								} else if ($subitem->fromlocation == "B") {
									$subitem->fromlocationname = "Next Available 'U' From Bottom";
									
								} else if ($subitem->fromlocation == "U") {
									$subitem->fromlocationname = "'U' Location";
								}
								
								if ($subitem->tolocation == "T") {
									$subitem->tolocationname = "Next Available 'U' From Top";
									
								} else if ($subitem->tolocation == "B") {
									$subitem->tolocationname = "Next Available 'U' From Bottom";
									
								} else if ($subitem->tolocation == "U") {
									$subitem->tolocationname = "'U' Location";
								}
								
								$quoteitem->panel->item[$subitemindex] = $subitem;
								
								$subitemindex++;
							}
						}
					}
				}
			}
			
	    	$_SESSION['QUOTATION'] = $this;
	    }
	    
	    public function rejectCancellation() {
	    	$this->updateCancellationStatus("N");
	    }
	    
	    public function approveCancellation() {
	    	$this->updateCancellationStatus("Y");
			
			return $this->confirmCancellation();
	    }
	    
	    private function updateCancellationStatus($status) {
			$this->notes = dateStampString($this->oldnotes, $this->notes, "CANCELLATION WORKFLOW NOTES");
		
			$qry = "UPDATE jomon_quoteheader SET " .
					"notes = '$this->notes' " .
					"WHERE id = $this->headerid";
			$result = mysql_query($qry);
				
			$qry = "SELECT B.id  " .
					"FROM jomon_cancelledjobflowheader A " .
					"INNER JOIN jomon_cancelledjobflowdetail B  " .
					"ON B.flowheaderid = A.id  " .
					"INNER JOIN jomon_userroles C  " .
					"ON C.roleid = B.roleid  " .
					"WHERE A.quoteid = $this->headerid " .
					"AND C.memberid = " . $_SESSION['SESS_MEMBER_ID'] . " " .
					"AND (B.status IS NULL OR B.status = ' ' OR B.status = 'N')";
			$result = mysql_query($qry);
			
			//Check whether the query was successful or not
			if($result) {
				while (($member = mysql_fetch_assoc($result))) {
		   			
					$qry = "UPDATE jomon_cancelledjobflowdetail SET " .
							"status = '$status', " .
							"processeddate = NOW(), " .
							"processedby = " . $_SESSION['SESS_MEMBER_ID'] . " " .
							"WHERE id = " . $member['id'];
					$itemresult = mysql_query($qry);
				   	
				   	if (! $itemresult) {
				   		die("Error UPDATE jomon_cancelledjobflowdetail:" . $qry . " - " . mysql_error());
				   	}
				}
				
			} else {
	   			die("Error SELECT FROM jomon_cancelledjobflowheader:" . $qry . " - " . mysql_error());
			}
	    }
	    
	    private function confirmCancellation() {
	    	$found = false;
			$qry = "SELECT B.id, B.status  " .
					"FROM jomon_cancelledjobflowheader A " .
					"INNER JOIN jomon_cancelledjobflowdetail B  " .
					"ON B.flowheaderid = A.id  " .
					"INNER JOIN jomon_userroles C  " .
					"ON C.roleid = B.roleid  " .
					"WHERE A.quoteid = $this->headerid " .
					"AND (B.status IS NULL OR B.status = ' ' OR B.status = 'N')";
			$result = mysql_query($qry);
			
			//Check whether the query was successful or not
			if($result) {
				while (($member = mysql_fetch_assoc($result))) {
					$found = true;
				}
			}
			
			if (! $found) {
	    		$this->status = "X";
	    		
				$qry = "UPDATE jomon_quoteheader SET " .
						"status = '$this->status' " .
						"WHERE id = $this->headerid";
				$result = mysql_query($qry);
			   	
			   	if (! $result) {
			   		die("Error UPDATE jomon_quoteheader:" . $qry . " - " . mysql_error());
			   	}
				   	
				if ($this->costcode == "CAPEXCCF") {
					$ordervalue = $this->getTotal();
					$qry = "UPDATE jomon_siteconfig SET currentcapexdealpovalue = currentcapexdealpovalue + $ordervalue";
					$result = mysql_query($qry);
					
				   	if (! $result) {
				   		die("Error UPDATE jomon_siteconfig (currentcapexdealpovalue):" . $qry . " - " . mysql_error());
				   	}
				}
				
				$qry = "SELECT DISTINCT B.roleid  " .
						"FROM jomon_cancelledjobflowheader A " .
						"INNER JOIN jomon_cancelledjobflowdetail B  " .
						"ON B.flowheaderid = A.id  " .
						"WHERE A.quoteid = $this->headerid ";
				$result = mysql_query($qry);
				
				//Check whether the query was successful or not
				if($result) {
					while (($member = mysql_fetch_assoc($result))) {
						$body =
							"<h2>Job " . $this->getHeaderID() . " has been cancelled</h2>" .
							$this->getEmailDetails() .
							"<h2>Reason</h2>" .
							"<p>" . $this->notes . "</p>";
							
						sendRoleMessage(
								$member['roleid'], 
								"Job Cancellation", 
								$body
							);
					}
				}
			   	
	    		$quote = $this->copy();
	    		$quote->saveThrowAway();
			}
			
			return ! $found;
	    }
	    
	    public function cancel($cancelnotes = "") {
	    	if ($this->headerid != 0) {
	    		$this->originalstatus = $this->status;
	    		$this->status = "P";
				$this->notes = dateStampString($this->oldnotes, $cancelnotes, "CANCELLATION NOTES");
	    		$cancelledby = $_SESSION['SESS_MEMBER_ID'];
    		
				$qry = "UPDATE jomon_quoteheader SET " .
						"status = '$this->status', " .
						"originalstatus = '$this->originalstatus', " .
						"notes = '$this->notes', " .
						"cancelledby = $cancelledby,  " .
						"cancelleddate = NOW()  " .
						"WHERE id = $this->headerid";
				$result = mysql_query($qry);
			   	
			   	if (! $result) {
			   		die("Error UPDATE jomon_quoteheader:" . $qry . " - " . mysql_error());
			   	}
	    		
	    		/* Reload. */
	    		$this->load($this->headerid);
	    		$column = "";
	    		
	    		if ($this->originalstatus == "A") {
	    			$column = "stageapproval";
					
	    		} else if ($this->originalstatus == "S") {
	    			$column = "stagescheduled";
					
	    		} else if ($this->originalstatus == "I") {
	    			$column = "stageceapproval";
					
	    		} else if ($this->originalstatus == "C") {
	    			$column = "stagecomplete";
					
	    		} else if ($this->originalstatus == "Q") {
	    			$column = "stageqa";
	    		}
	    		
	    		if ($column != "") {
					$qry = "SELECT $column FROM jomon_siteconfig";
					$result = mysql_query($qry);
					$stage = "";
					
					//Check whether the query was successful or not
					if($result) {
						while (($member = mysql_fetch_assoc($result))) {
							$stage = $member[$column];
						}
						
					} else {
				   		die("Error SELECT $column FROM jomon_siteconfig:" . $qry . " - " . mysql_error());
					}
	
					$qry = "INSERT INTO jomon_cancelledjobflowheader (quoteid, routeid) VALUES ($this->headerid, '$stage')";
					$result = mysql_query($qry);
					$flowheaderid = mysql_insert_id();
	
				   	if (! $result) {
				   		die("Error INSERT INTO jomon_cancelledjobflowheader:" . $qry . " - " . mysql_error());
				   	}
					
					$qry = "SELECT roleid " .
							"FROM jomon_roleroutes " .
							"WHERE routeid = '$stage'";
					$result = mysql_query($qry);
					
					//Check whether the query was successful or not
					if($result) {
						while (($member = mysql_fetch_assoc($result))) {
							$role = $member['roleid'];
							$url = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
							$app = substr($url, lastIndexOf($url, "/") + 1);
							$url = str_replace($app, "confirmcancellation.php?id=" . $this->headerid, $url);
							
							$qry = "INSERT INTO jomon_cancelledjobflowdetail (flowheaderid, roleid, status) VALUES ($flowheaderid, '$role', ' ')";
							$resultinsert = mysql_query($qry);
			
						   	if (! $resultinsert) {
						   		die("Error INSERT INTO jomon_cancelledjobflowheader:" . $qry . " - " . mysql_error());
						   	}
							
							$body =
								"<h2>Cancellation approval required for the following job.</h2>" .
								$this->getEmailDetails() .
								"<h2>Reason</h2>" .
								"<p>" . $this->notes . "</p>".
								"<a href='$url'>Please Click here to Approve or Reject this cancellation request</a><br>";
							
							sendRoleMessage(
									$role,
									"Cancellation Approval", 
									$body
								);
						}
					}
	    		} else {
	    			if ($this->status == "N" || $this->status == "R") {
	    				/* Draft mode can be cancelled. */
			    		$this->status = "X";
			    		
						$qry = "UPDATE jomon_quoteheader SET " .
								"status = '$this->status' " .
								"WHERE id = $this->headerid";
						$result = mysql_query($qry);
	    			}	
	    		}
	    		
	    		return null;
				
	    	} else {
	    		$quote = $this->copy();
	    		$quote->saveThrowAway(false);
	    		
	    		return $quote;
	    	}
	    }
	    
	    public function copy() {
	    	$quote = new QuotationHeader();
		    $quote->siteid = $this->siteid;
		    $quote->contactid = $this->contactid;
		    $quote->costcode = $this->costcode;
		    $quote->oldcostcode = $this->oldcostcode;
		    $quote->costcodedesc = $this->costcodedesc;
		    $quote->customer = $this->customer;
		    $quote->ccf = $this->ccf;
		    $quote->customerpo = $this->customerpo;
		    $quote->sungardpo = $this->sungardpo;
		    $quote->ccfpath = $this->ccfpath;
		    $quote->cabinstalldate = $this->cabinstalldate;
		    $quote->requiredbydate = $this->requiredbydate;
		    $quote->oldnotes = $this->oldnotes;
		    $quote->notes = $this->notes;
		    $quote->status = $this->status;
		    $quote->originalstatus = $this->originalstatus;
		    $quote->approvalid = $this->approvalid;
		    $quote->createdby = $this->createdby;
		    $quote->createddate = $this->createddate;
		    $quote->cancelledby = $this->cancelledby;
		    $quote->cancelleddate = $this->cancelleddate;
		    $quote->approvedby = $this->approvedby;
		    $quote->approveddate = $this->approveddate;
		    $quote->scheduledby =  $this->scheduledby;
		    $quote->scheduleddate = $this->scheduleddate;
		    $quote->ceapprovedby =  $this->ceapprovedby;
		    $quote->ceapproveddate = $this->ceapproveddate;
		    $quote->archivedby = $this->archivedby;
		    $quote->archiveddate = $this->archiveddate;
		    $quote->completedby = $this->completedby;
		    $quote->completeddate = $this->completeddate;
		    $quote->internalapprovalcode = $this->internalapprovalcode;
		    $quote->qaby = $this->qaby;
		    $quote->qadate = $this->qadate;
		    $quote->jobprefix = $this->jobprefix;
		    $quote->prefix = $this->prefix;
		    $quote->jobid = $this->jobid;
		    
		    for ($i = 0; $i < count($this->items); $i++) {
		    	$item = $this->items[$i];
		    	
		    	$newItem = new QuotationItem();
			    $newItem->type = $item->type;
			    $newItem->cat1 = $item->cat1;
			    $newItem->cat2 = $item->cat2;
			    $newItem->cat3 = $item->cat3;
			    $newItem->productid = $item->productid;
			    $newItem->productdesc = $item->productdesc;
			    $newItem->qty = $item->qty;
			    $newItem->itemnumber = $item->itemnumber;
			    $newItem->price = $item->price;
			    $newItem->total = $item->total;
			    $newItem->length = $item->length;
			    $newItem->productlengthid = $item->productlengthid;
			    $newItem->inout = $item->inout;
			    $newItem->notes = $item->notes;
			    $newItem->deleted = $item->deleted;
			    $newItem->status = $item->status;
			    $newItem->manneddays = $item->manneddays;
			    
		    	$quote->add($newItem);
		    }
		    
		    return $quote;
	    }
	    
	    public function saveThrowAway($throwaway = true) {
	    	if ($this->headerid != 0) {
	    		$updated = true;
	    		$mysql_cabinstalldate = null;
	    		$mysql_requiredby = null;
	    		
	    		if ($this->cabinstalldate != "") {
					$mysql_cabinstalldate = substr($this->cabinstalldate, 6, 4 ) . "-" . substr($this->cabinstalldate, 3, 2 ) . "-" . substr($this->cabinstalldate, 0, 2 );
	    		}
	    		
	    		if ($this->requiredbydate != null) {
					$mysql_requiredby = substr($this->requiredbydate, 6, 4 ) . "-" . substr($this->requiredbydate, 3, 2 ) . "-" . substr($this->requiredbydate, 0, 2 );
	    		}
	    		
				$this->notes = mysql_escape_string($this->notes);
				$this->ccfpath = mysql_escape_string($this->ccfpath);
				
				$qry = "UPDATE jomon_cancelledquoteheader SET " .
						"siteid = $this->siteid, " .
						"customer = '$this->customer', " .
						"internalapprovalcode = '$this->internalapprovalcode', " .
						"ccf = '$this->ccf', " .
						"customerpo = '$this->customerpo', " .
						"contactid = $this->contactid, " .
						"cabinstalldate = '$mysql_cabinstalldate', " .
						"requiredby = '$mysql_requiredby', " .
						"costcode = '$this->costcode', " .
						"ccfpath = '$this->ccfpath', " .
						"sungardpo = '$this->sungardpo', " .
						"notes = '$this->notes' " .
						"WHERE id = $this->headerid";
				$result = mysql_query($qry);
			   	
			   	if (! $result) {
			   		die("Error UPDATE jomon_cancelledquoteheader:" . $qry . " - " . mysql_error());
			   	}
			   	
			   	$linenumber = 0;
			   	
				for ($i = 0; $i < $this->itemCount(); $i++) {
					$item = $this->get($i);
					
					if ($item->id != 0 && $item->id > $linenumber) {
						$linenumber = $item->itemnumber + 1;
					}
				}
	    		
				for ($i = 0; $i < $this->itemCount(); $i++) {
					$item = $this->get($i);
					
					if (! $item->deleted) {
						if ($item->id != 0) {
							$qry = "UPDATE jomon_cancelledquoteitem SET " .
									"description = '$item->productdesc', " .
									"qty = $item->qty, " .
									"cat1 = $item->cat1, " .
									"cat2 = $item->cat2, " .
									"cat3 = $item->cat3, " .
									"manneddays = $item->manneddays, " .
									"labourratehours = '$item->inout', " .
									"price = $item->price, " .
									"total = $item->total, " .
									"notes = '$item->notes', " .
									"productid = $item->productid, " .
									"lengthid = $item->productlengthid " .
									"WHERE id = $item->id ";
							$result = mysql_query($qry);
				   	
						   	if (! $result) {
						   		die("Error UPDATE jomon_cancelledquoteitem:" . mysql_error());
						   	}
						   	
						} else {
							$description = $item->productdesc;
							$qty = $item->qty;
							$price=  $item->price;
							$total = $item->total;
							$notes = $item->notes;
							$item->itemnumber = $linenumber;
							
							$qry = "INSERT INTO jomon_cancelledquoteitem  " .
									"(headerid, linenumber, description, qty, price, total, notes, productid, " .
									"lengthid, cat1, cat2, cat3, manneddays, labourratehours, createdby, createddate) " .
									"VALUES " .
									"($this->headerid, $linenumber, '$description', $qty, $price, $total, '$notes', $item->productid, " .
									"$item->productlengthid, $item->cat1, $item->cat2, $item->cat3, " .
									"$item->manneddays, '$item->inout', " . $_SESSION['SESS_MEMBER_ID'] . ", NOW())";
							$result = mysql_query($qry);
							$linenumber++;
							
						   	$itemid =  mysql_insert_id();
						   	$item->id = $itemid;

						   	if (! $result) {
						   		die("Error INSERT INTO jomon_cancelledquoteitem:" . mysql_error());
						   	}
						   	
						   	if ($item->productdesc == "Bespoke") {
								sendRoleMessage(
										'DATATECHNIQUES',
										"Bespoke Quote Item",
										"<h1>Quotation " . $this->prefix . sprintf("%04d", $this->headerid) . " contains a bespoke quote item</h1>" . str_replace("\n", "<br/>", $item->notes)
									);
						   	}
						}
					   	
						$qry = "DELETE FROM jomon_cancelledquoteitemlongline WHERE quoteitemid = $item->id";
						$result = mysql_query($qry);
			   	
					   	if (! $result) {
					   		die("Error DELETE FROM jomon_cancelledquoteitemlongline:" . $qry . " - " . mysql_error());
					   	}
					   	
					   	if ($item->longline != null) {
							$counter = count($item->longline->item);
							
							for ($x = 0; $x < $counter; $x++) {
								
								$qry = "INSERT INTO jomon_cancelledquoteitemlongline " .
										"(quoteitemid, fromareaid, fromcabinet, toareaid, tocabinet, notes, createdby, createddate) " .
										"VALUES " .
										"($item->id, " .
										"" . $item->longline->item[$x]->fromarea . ", " .
										"'" . $item->longline->item[$x]->fromcabinet . "', " .
										"" . $item->longline->item[$x]->toarea . ", " .
										"'" . $item->longline->item[$x]->tocabinet . "', " .
										"'" . mysql_escape_string($item->longline->item[$x]->notes) . "', " .
										$_SESSION['SESS_MEMBER_ID'] . ", NOW())";
								$result = mysql_query($qry);
								
							   	if (! $result) {
							   		die("Error INSERT INTO jomon_cancelledquoteitemlongline:" . mysql_error());
							   	}
							}
					   	}
					   	
						$qry = "DELETE FROM jomon_cancelledquoteitempanel WHERE quoteitemid = $item->id";
						$result = mysql_query($qry);
								
					   	if (! $result) {
					   		die("Error DELETE FROM jomon_cancelledquoteitempanel:" . mysql_error());
					   	}
					   	
					   	if ($item->panel != null) {
							$counter = count($item->panel->item);
							
							for ($x = 0; $x < $counter; $x++) {
								
								$qry = "INSERT INTO jomon_cancelledquoteitempanel " .
										"(quoteitemid, fromareaid, fromcabinet, fromposition, fromlocation, fromuloc, " .
										"toareaid, tocabinet, toposition, tolocation, touloc, createdby, createddate) " .
										"VALUES " .
										"($item->id, " .
										"" . $item->panel->item[$x]->fromarea . ", " .
										"'" . $item->panel->item[$x]->fromcabinet . "', " .
										"'" . $item->panel->item[$x]->fromposition . "', " .
										"'" . $item->panel->item[$x]->fromlocation . "', " .
										"'" . $item->panel->item[$x]->fromulocation . "', " .
										"" . $item->panel->item[$x]->toarea . ", " .
										"'" . $item->panel->item[$x]->tocabinet . "', " .
										"'" . $item->panel->item[$x]->toposition . "', " .
										"'" . $item->panel->item[$x]->tolocation . "', " .
										"'" . $item->panel->item[$x]->toulocation . "', " .
										$_SESSION['SESS_MEMBER_ID'] . ", NOW())";
								$result = mysql_query($qry);
								
							   	if (! $result) {
							   		die("Error INSERT INTO jomon_cancelledquoteitempanel:" . mysql_error());
							   	}
							}
					   	}
						
					} else {
						$qry = "DELETE FROM jomon_cancelledquoteitemlongline WHERE quoteitemid = $item->id";
						$result = mysql_query($qry);
								
					   	if (! $result) {
					   		die("Error DELETE FROM jomon_cancelledquoteitemlongline:" . mysql_error());
					   	}
					   	
						$qry = "DELETE FROM jomon_cancelledquoteitempanel WHERE quoteitemid = $item->id";
						$result = mysql_query($qry);
								
					   	if (! $result) {
					   		die("Error DELETE FROM jomon_cancelledquoteitempanel:" . mysql_error());
					   	}
					   	
						$qry = "DELETE FROM jomon_cancelledquoteitem WHERE id = $item->id";
						$result = mysql_query($qry);
								
					   	if (! $result) {
					   		die("Error DELETE FROM jomon_cancelledquoteitem:" . mysql_error());
					   	}
					}
				}
				
	    	} else {
	    		$updated = false;
				$prefix = "QC-";
				$mysql_cabinstalldate = substr($this->cabinstalldate, 6, 4 ) . "-" . substr($this->cabinstalldate, 3, 2 ) . "-" . substr($this->cabinstalldate, 0, 2 );
				$mysql_requiredby = substr($this->requiredbydate, 6, 4 ) . "-" . substr($this->requiredbydate, 3, 2 ) . "-" . substr($this->requiredbydate, 0, 2 );
				$this->notes = mysql_escape_string($this->notes);
				
				$qry = "INSERT INTO jomon_cancelledquoteheader  " .
						"(prefix, status, siteid, customer, ccf, customerpo, contactid, internalapprovalcode, " .
						"cabinstalldate, ccfpath, sungardpo, requiredby, costcode, notes, createdby, createddate, throwaway) " .
						"VALUES " .
						"('$prefix', 'N', $this->siteid, '$this->customer', '$this->ccf', '$this->customerpo', $this->contactid, '$this->internalapprovalcode', " .
						"'$mysql_cabinstalldate', '$this->ccfpath', '$this->sungardpo', '$mysql_requiredby', '$this->costcode', '$this->notes', " . $_SESSION['SESS_MEMBER_ID'] . ", NOW(), '$throwaway')";
				$result = mysql_query($qry);
			   	$this->headerid =  mysql_insert_id();
			   	$this->createdby = $_SESSION['SESS_MEMBER_ID'];
			   	$this->createddate = date("d/m/Y");
			   	
			   	if (! $result) {
			   		die("Error INSERT INTO jomon_cancelledquoteheader:" . $qry . " - " . mysql_error());
			   	}
			   	
			   	$linenumber = 0;
			
				for ($i = 0; $i < $this->itemCount(); $i++) {
					$item = $this->get($i);
					
					if (! $item->deleted) {
						$linenumber++;
						$description = $item->productdesc;
						$qty = $item->qty;
						$price=  $item->price;
						$total = $item->total;
						$notes = $item->notes;
						$item->itemnumber = $linenumber;
						
						$qry = "INSERT INTO jomon_cancelledquoteitem  " .
								"(headerid, linenumber, description, qty, price, total, notes, productid, " .
								"lengthid, cat1, cat2, cat3, manneddays, labourratehours, createdby, createddate) " .
								"VALUES " .
								"($this->headerid, $linenumber, '$description', $qty, $price, $total, '$notes', $item->productid, " .
								"$item->productlengthid, $item->cat1, $item->cat2, " .
								"$item->cat3, $item->manneddays, '$item->inout', " . $_SESSION['SESS_MEMBER_ID'] . ", NOW())";
						$result = mysql_query($qry);
			   	
					   	if (! $result) {
					   		die("Error (NEW) INSERT INTO jomon_cancelledquoteitem:" . mysql_error());
					   	}
					   	
					   	$itemid =  mysql_insert_id();
					   	$item->id = $itemid;
					   	
					   	if ($item->longline != null) {
							$counter = count($item->longline->item);
							
							for ($x = 0; $x < $counter; $x++) {
								
								$qry = "INSERT INTO jomon_cancelledquoteitemlongline " .
										"(quoteitemid, fromareaid, fromcabinet, toareaid, tocabinet, notes, createdby, createddate) " .
										"VALUES " .
										"($itemid, " .
										"" . $item->longline->item[$x]->fromarea . ", " .
										"'" . $item->longline->item[$x]->fromcabinet . "', " .
										"" . $item->longline->item[$x]->toarea . ", " .
										"'" . $item->longline->item[$x]->tocabinet . "', " .
										"'" . mysql_escape_string($item->longline->item[$x]->notes) . "', " .
										$_SESSION['SESS_MEMBER_ID'] . ", NOW())";
								$result = mysql_query($qry);
								
							   	if (! $result) {
							   		die("Error INSERT INTO jomon_cancelledquoteitemlongline:" . mysql_error());
							   	}
							}
					   	}
					   	
					   	if ($item->panel != null) {
							$counter = count($item->panel->item);
							
							for ($x = 0; $x < $counter; $x++) {
								
								$qry = "INSERT INTO jomon_cancelledquoteitempanel " .
										"(quoteitemid, fromareaid, fromcabinet, fromposition, fromlocation, fromuloc, " .
										"toareaid, tocabinet, toposition, tolocation, touloc, createdby, createddate) " .
										"VALUES " .
										"($itemid, " .
										"" . $item->panel->item[$x]->fromarea . ", " .
										"'" . $item->panel->item[$x]->fromcabinet . "', " .
										"'" . $item->panel->item[$x]->fromposition . "', " .
										"'" . $item->panel->item[$x]->fromlocation . "', " .
										"'" . $item->panel->item[$x]->fromulocation . "', " .
										"" . $item->panel->item[$x]->toarea . ", " .
										"'" . $item->panel->item[$x]->tocabinet . "', " .
										"'" . $item->panel->item[$x]->toposition . "', " .
										"'" . $item->panel->item[$x]->tolocation . "', " .
										"'" . $item->panel->item[$x]->toulocation . "', " .
										$_SESSION['SESS_MEMBER_ID'] . ", NOW())";
								$result = mysql_query($qry);
								
							   	if (! $result) {
							   		die("Error INSERT INTO jomon_cancelledquoteitempanel:" . mysql_error());
							   	}
							}
					   	}
					}
				}
			}
	    }
	    
	    public function save() {
	    	$updated = false;
	    	
	    	if ($this->headerid != 0) {
	    		$updated = true;
	    		$mysql_cabinstalldate = null;
	    		$mysql_requiredby = null;
	    		
	    		if ($this->cabinstalldate != "") {
					$mysql_cabinstalldate = substr($this->cabinstalldate, 6, 4 ) . "-" . substr($this->cabinstalldate, 3, 2 ) . "-" . substr($this->cabinstalldate, 0, 2 );
	    		}
	    		
	    		if ($this->requiredbydate != null) {
					$mysql_requiredby = substr($this->requiredbydate, 6, 4 ) . "-" . substr($this->requiredbydate, 3, 2 ) . "-" . substr($this->requiredbydate, 0, 2 );
	    		}
	    		
				$this->notes = mysql_escape_string($this->notes);
				$this->ccfpath = mysql_escape_string($this->ccfpath);
				
				$qry = "UPDATE jomon_quoteheader SET " .
						"siteid = $this->siteid, " .
						"customer = '$this->customer', " .
						"internalapprovalcode = '$this->internalapprovalcode', " .
						"ccf = '$this->ccf', " .
						"customerpo = '$this->customerpo', " .
						"contactid = $this->contactid, " .
						"cabinstalldate = '$mysql_cabinstalldate', " .
						"requiredby = '$mysql_requiredby', " .
						"costcode = '$this->costcode', " .
						"ccfpath = '$this->ccfpath', " .
						"sungardpo = '$this->sungardpo', " .
						"notes = '$this->notes' " .
						"WHERE id = $this->headerid";
				$result = mysql_query($qry);
			   	
			   	if (! $result) {
			   		die("Error UPDATE jomon_quoteheader:" . $qry . " - " . mysql_error());
			   	}
			   	
			   	$linenumber = 0;
			   	
				for ($i = 0; $i < $this->itemCount(); $i++) {
					$item = $this->get($i);
					
					if ($item->id != 0 && $item->id > $linenumber) {
						$linenumber = $item->itemnumber + 1;
					}
				}
	    		
				for ($i = 0; $i < $this->itemCount(); $i++) {
					$item = $this->get($i);
					
					if (! $item->deleted) {
						if ($item->id != 0) {
							$qry = "UPDATE jomon_quoteitem SET " .
									"description = '$item->productdesc', " .
									"qty = $item->qty, " .
									"cat1 = $item->cat1, " .
									"cat2 = $item->cat2, " .
									"cat3 = $item->cat3, " .
									"manneddays = $item->manneddays, " .
									"labourratehours = '$item->inout', " .
									"price = $item->price, " .
									"total = $item->total, " .
									"notes = '$item->notes', " .
									"productid = $item->productid, " .
									"lengthid = $item->productlengthid " .
									"WHERE id = $item->id ";
							$result = mysql_query($qry);
				   	
						   	if (! $result) {
						   		die("Error UPDATE jomon_quoteitem:" . mysql_error());
						   	}
						   	
						} else {
							$description = $item->productdesc;
							$qty = $item->qty;
							$price=  $item->price;
							$total = $item->total;
							$notes = $item->notes;
							$item->itemnumber = $linenumber;
							
							$qry = "INSERT INTO jomon_quoteitem  " .
									"(headerid, linenumber, description, qty, price, total, notes, productid, " .
									"lengthid, cat1, cat2, cat3, manneddays, labourratehours, createdby, createddate) " .
									"VALUES " .
									"($this->headerid, $linenumber, '$description', $qty, $price, $total, '$notes', $item->productid, " .
									"$item->productlengthid, $item->cat1, $item->cat2, $item->cat3, " .
									"$item->manneddays, '$item->inout', " . $_SESSION['SESS_MEMBER_ID'] . ", NOW())";
							$result = mysql_query($qry);
							$linenumber++;
							
						   	$itemid =  mysql_insert_id();
						   	$item->id = $itemid;

						   	if (! $result) {
						   		die("Error INSERT INTO jomon_quoteitem:" . mysql_error());
						   	}
						   	
						   	if ($item->productdesc == "Bespoke") {
								sendRoleMessage(
										'DATATECHNIQUES',
										"Bespoke Quote Item",
										"<h1>Quotation " . $this->prefix . sprintf("%04d", $this->headerid) . " contains a bespoke quote item</h1>" . str_replace("\n", "<br/>", $item->notes)
									);
						   	}
						}
					   	
						$qry = "DELETE FROM jomon_quoteitemlongline WHERE quoteitemid = $item->id";
						$result = mysql_query($qry);
			   	
					   	if (! $result) {
					   		die("Error DELETE FROM jomon_quoteitemlongline:" . $qry . " - " . mysql_error());
					   	}
					   	
					   	if ($item->longline != null) {
							$counter = count($item->longline->item);
							
							for ($x = 0; $x < $counter; $x++) {
								
								$qry = "INSERT INTO jomon_quoteitemlongline " .
										"(quoteitemid, fromareaid, fromcabinet, toareaid, tocabinet, notes, createdby, createddate) " .
										"VALUES " .
										"($item->id, " .
										"" . $item->longline->item[$x]->fromarea . ", " .
										"'" . $item->longline->item[$x]->fromcabinet . "', " .
										"" . $item->longline->item[$x]->toarea . ", " .
										"'" . $item->longline->item[$x]->tocabinet . "', " .
										"'" . mysql_escape_string($item->longline->item[$x]->notes) . "', " .
										$_SESSION['SESS_MEMBER_ID'] . ", NOW())";
								$result = mysql_query($qry);
								
							   	if (! $result) {
							   		die("Error INSERT INTO jomon_quoteitemlongline:" . mysql_error() . " - " . $qry);
							   	}
							}
					   	}
					   	
						$qry = "DELETE FROM jomon_quoteitempanel WHERE quoteitemid = $item->id";
						$result = mysql_query($qry);
								
					   	if (! $result) {
					   		die("Error DELETE FROM jomon_quoteitempanel:" . mysql_error());
					   	}
					   	
					   	if ($item->panel != null) {
							$counter = count($item->panel->item);
							
							for ($x = 0; $x < $counter; $x++) {
								
								$qry = "INSERT INTO jomon_quoteitempanel " .
										"(quoteitemid, fromareaid, fromcabinet, fromposition, fromlocation, fromuloc, " .
										"toareaid, tocabinet, toposition, tolocation, touloc, createdby, createddate) " .
										"VALUES " .
										"($item->id, " .
										"" . $item->panel->item[$x]->fromarea . ", " .
										"'" . $item->panel->item[$x]->fromcabinet . "', " .
										"'" . $item->panel->item[$x]->fromposition . "', " .
										"'" . $item->panel->item[$x]->fromlocation . "', " .
										"'" . $item->panel->item[$x]->fromulocation . "', " .
										"" . $item->panel->item[$x]->toarea . ", " .
										"'" . $item->panel->item[$x]->tocabinet . "', " .
										"'" . $item->panel->item[$x]->toposition . "', " .
										"'" . $item->panel->item[$x]->tolocation . "', " .
										"'" . $item->panel->item[$x]->toulocation . "', " .
										$_SESSION['SESS_MEMBER_ID'] . ", NOW())";
								$result = mysql_query($qry);
								
							   	if (! $result) {
							   		die("Error INSERT INTO jomon_quoteitempanel:" . mysql_error());
							   	}
							}
					   	}
						
					} else {
						$qry = "DELETE FROM jomon_quoteitemlongline WHERE quoteitemid = $item->id";
						$result = mysql_query($qry);
								
					   	if (! $result) {
					   		die("Error DELETE FROM jomon_quoteitemlongline:" . mysql_error());
					   	}
					   	
						$qry = "DELETE FROM jomon_quoteitempanel WHERE quoteitemid = $item->id";
						$result = mysql_query($qry);
								
					   	if (! $result) {
					   		die("Error DELETE FROM jomon_quoteitempanel:" . mysql_error());
					   	}
					   	
						$qry = "DELETE FROM jomon_quoteitem WHERE id = $item->id";
						$result = mysql_query($qry);
								
					   	if (! $result) {
					   		die("Error DELETE FROM jomon_quoteitem:" . mysql_error());
					   	}
					}
				}
				
				if ($this->oldcostcode != $this->costcode) {
					$ordervalue = $this->getTotal();
					
					if ($this->costcode == "CAPEXCCF") {
						$qry = "UPDATE jomon_siteconfig SET currentcapexdealpovalue = currentcapexdealpovalue - $ordervalue";
						$result = mysql_query($qry);
						
					   	if (! $result) {
					   		die("Error UPDATE jomon_siteconfig (currentcapexdealpovalue):" . $qry . " - " . mysql_error());
					   	}
					}
					
					if ($this->oldcostcode == "CAPEXCCF") {
						$qry = "UPDATE jomon_siteconfig SET currentcapexdealpovalue = currentcapexdealpovalue + $ordervalue";
						$result = mysql_query($qry);
						
					   	if (! $result) {
					   		die("Error UPDATE jomon_siteconfig (currentcapexdealpovalue):" . $qry . " - " . mysql_error());
					   	}
					}
					
					sendRoleMessage(
							'FINANCE',
							"Cost Code Changed",
							"<h1>Quotation " . $this->prefix . sprintf("%04d", $this->headerid) . " cost code has been altered</h1>" . $this->getEmailDetails()
						);
				}
				
	    	} else {
	    		$updated = false;
				$prefix = "SQ-194SG";
	    		$mysql_cabinstalldate = "2000-01-01";
	    		$mysql_requiredby = "2000-01-01";
	    		
	    		if ($this->cabinstalldate != "") {
					$mysql_cabinstalldate = substr($this->cabinstalldate, 6, 4 ) . "-" . substr($this->cabinstalldate, 3, 2 ) . "-" . substr($this->cabinstalldate, 0, 2 );
	    		}
	    		
	    		if ($this->requiredbydate != null) {
					$mysql_requiredby = substr($this->requiredbydate, 6, 4 ) . "-" . substr($this->requiredbydate, 3, 2 ) . "-" . substr($this->requiredbydate, 0, 2 );
	    		}
	    		
				$this->notes = mysql_escape_string($this->notes);
				$this->ccfpath = mysql_escape_string($this->ccfpath);
				
				$qry = "INSERT INTO jomon_quoteheader  " .
						"(prefix, status, siteid, customer, ccf, customerpo, contactid, internalapprovalcode, " .
						"cabinstalldate, ccfpath, sungardpo, requiredby, costcode, notes, createdby, createddate) " .
						"VALUES " .
						"('$prefix', 'N', $this->siteid, '$this->customer', '$this->ccf', '$this->customerpo', $this->contactid, '$this->internalapprovalcode', " .
						"'$mysql_cabinstalldate', '$this->ccfpath', '$this->sungardpo', '$mysql_requiredby', '$this->costcode', '$this->notes', " . $_SESSION['SESS_MEMBER_ID'] . ", NOW())";
				$result = mysql_query($qry);
			   	$this->headerid =  mysql_insert_id();
			   	$this->createdby = $_SESSION['SESS_MEMBER_ID'];
			   	$this->createddate = date("d/m/Y");
			   	
			   	if (! $result) {
			   		die("Error INSERT INTO jomon_quoteheader:" . $qry . " - " . mysql_error());
			   	}
				
				$qry = "UPDATE jomon_documents SET " .
						"headerid = $this->headerid," .
						"sessionid = NULL " .
						"WHERE sessionid = '" . session_id() . "'";
				$result = mysql_query($qry);
			   	
			   	if (! $result) {
			   		die("Error UPDATE jomon_documents:" . $qry . " - " . mysql_error());
			   	}
			   	
			   	$linenumber = 0;
			
				for ($i = 0; $i < $this->itemCount(); $i++) {
					$item = $this->get($i);
					
					if (! $item->deleted) {
						$linenumber++;
						$description = $item->productdesc;
						$qty = $item->qty;
						$price=  $item->price;
						$total = $item->total;
						$notes = $item->notes;
						$item->itemnumber = $linenumber;
						
						$qry = "INSERT INTO jomon_quoteitem  " .
								"(headerid, linenumber, description, qty, price, total, notes, productid, " .
								"lengthid, cat1, cat2, cat3, manneddays, labourratehours, createdby, createddate) " .
								"VALUES " .
								"($this->headerid, $linenumber, '$description', $qty, $price, $total, '$notes', $item->productid, " .
								"$item->productlengthid, $item->cat1, $item->cat2, " .
								"$item->cat3, $item->manneddays, '$item->inout', " . $_SESSION['SESS_MEMBER_ID'] . ", NOW())";
						$result = mysql_query($qry);
			   	
					   	if (! $result) {
					   		die("Error (NEW) INSERT INTO jomon_quoteitem:" . mysql_error());
					   	}
					   	
					   	$itemid =  mysql_insert_id();
					   	$item->id = $itemid;
					   	
					   	if ($item->productdesc == "Bespoke") {
							sendRoleMessage(
									'DATATECHNIQUES',
									"Bespoke Quote Item",
									"<h1>Quotation " . $this->prefix . sprintf("%04d", $this->headerid) . " contains a bespoke quote item</h1>" . str_replace("\n", "<br/>", $item->notes)
								);
					   	}
						   	
					   	if ($item->longline != null) {
							$counter = count($item->longline->item);
							
							for ($x = 0; $x < $counter; $x++) {
								$qry = "INSERT INTO jomon_quoteitemlongline " .
										"(quoteitemid, fromareaid, fromcabinet, toareaid, tocabinet, notes, createdby, createddate) " .
										"VALUES " .
										"($itemid, " .
										"" . $item->longline->item[$x]->fromarea . ", " .
										"'" . $item->longline->item[$x]->fromcabinet . "', " .
										"" . $item->longline->item[$x]->toarea . ", " .
										"'" . $item->longline->item[$x]->tocabinet . "', " .
										"'" . mysql_escape_string($item->longline->item[$x]->notes) . "', " .
										$_SESSION['SESS_MEMBER_ID'] . ", NOW())";
								$result = mysql_query($qry);
								
							   	if (! $result) {
							   		die("Error INSERT INTO jomon_quoteitemlongline:" . mysql_error());
							   	}
							}
					   	}
					   	
					   	if ($item->panel != null) {
							$counter = count($item->panel->item);
							
							for ($x = 0; $x < $counter; $x++) {
								
								$qry = "INSERT INTO jomon_quoteitempanel " .
										"(quoteitemid, fromareaid, fromcabinet, fromposition, fromlocation, fromuloc, " .
										"toareaid, tocabinet, toposition, tolocation, touloc, createdby, createddate) " .
										"VALUES " .
										"($itemid, " .
										"" . $item->panel->item[$x]->fromarea . ", " .
										"'" . $item->panel->item[$x]->fromcabinet . "', " .
										"'" . $item->panel->item[$x]->fromposition . "', " .
										"'" . $item->panel->item[$x]->fromlocation . "', " .
										"'" . $item->panel->item[$x]->fromulocation . "', " .
										"" . $item->panel->item[$x]->toarea . ", " .
										"'" . $item->panel->item[$x]->tocabinet . "', " .
										"'" . $item->panel->item[$x]->toposition . "', " .
										"'" . $item->panel->item[$x]->tolocation . "', " .
										"'" . $item->panel->item[$x]->toulocation . "', " .
										$_SESSION['SESS_MEMBER_ID'] . ", NOW())";
								$result = mysql_query($qry);
								
							   	if (! $result) {
							   		die("Error INSERT INTO jomon_quoteitempanel:" . mysql_error());
							   	}
							}
					   	}
					}
				}
				
				if ($this->originalthrowawayquoteid != 0) {
					$qry = "DELETE FROM jomon_cancelledquoteitem " .
							"WHERE headerid = $this->originalthrowawayquoteid";
					$result = mysql_query($qry);
					
				   	if (! $result) {
				   		die("DELETE FROM jomon_cancelledquoteitem:" . $qry . " - " . mysql_error());
				   	}
					
					$qry = "DELETE FROM jomon_cancelledquoteheader " .
							"WHERE id = $this->originalthrowawayquoteid";
					$result = mysql_query($qry);
					
				   	if (! $result) {
				   		die("DELETE FROM jomon_cancelledquoteheader:" . $qry . " - " . mysql_error());
				   	}
				}
	    	}
	    	
	    	$this->load($this->headerid);

			if (! $updated) {
				sendRoleMessage(
						'FINANCE',
						"Quotation Created",
						"<h1>Quotation " . $this->prefix . sprintf("%04d", $this->headerid) . " has been created by " . GetUserName() . "</h1>" . $this->getEmailDetails()
					);
				
			} else {
				sendRoleMessage(
						'FINANCE',
						"Quotation Amended",
						"<h1>Quotation " . $this->prefix . sprintf("%04d", $this->headerid) . " has been amended by " . GetUserName() . "</h1>" . $this->getEmailDetails()
					);
			}
	    }
	}

?>
