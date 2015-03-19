<?php 
	require_once("quotationitem.php");
	require_once("system-db.php");
	
	start_db();
	initialise_db();
	
	$header = $_SESSION['QUOTATION'];
	
	if (isset($_POST['itemnumber']) && $_POST['itemnumber'] != "") {
		$item = $header->get($_POST['itemnumber']);
		
	} else {
		$item = new QuotationItem();
	}
	
	$item->type = $_POST['type'];
	$item->cat1 = $_POST['category'];
	
	if ($_POST['type'] == "Copper Panels" ||
	    $_POST['type'] == "Copper") {
		$productlengthid = $_POST['copperlength'];
		$item->productlengthid = $productlengthid;
		$item->qty = $_POST['copperqty'];
		$item->productid = $_POST['copperproduct'];
		$item->price = 0;
		$item->cat2 = $_POST['longlinepanelcategory'];
		$item->cat3 = $_POST['coppercat1'];
		
		$qry = "SELECT length " .
				"FROM jomon_productlengths " .
				"WHERE id = $productlengthid";
		$result = mysql_query($qry);
		
		//Check whether the query was successful or not
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				$item->length = $member['length'];
			}
		} else {
			die("Error:" . mysql_error());
		}
		
		$item->productdesc = $_POST['copperproductdesc'] . " (" . $item->length . "M)";
		
		$qry = "SELECT price " .
				"FROM jomon_pricebreaks " .
				"WHERE productlengthid = $productlengthid " .
				"AND ((fromunit <= " . $item->qty . " " .
				"AND tounit >= " . $item->qty . " ) OR  " .
				"(fromunit <= " . $item->qty . " " .
				"AND tounit = 0))";
		$result = mysql_query($qry);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				$item->price = $member['price'];
			}
		} else {
			die("Error:" . mysql_error());
		}
		
	} else if ($_POST['type'] == "Fibre Panels" ||
	           $_POST['type'] == "Fibre") {
		$productlengthid = $_POST['fibrelength'];
		$item->productlengthid = $productlengthid;
		$item->qty = $_POST['fibreqty'];
		$item->productid = $_POST['fibreproduct'];
		$item->price = 0;
		$item->cat2 = $_POST['longlinepanelcategory'];
		$item->cat3 = $_POST['fibrecat1'];
		
		$qry = "SELECT length " .
				"FROM jomon_productlengths " .
				"WHERE id = $productlengthid";
		$result = mysql_query($qry);
		
		//Check whether the query was successful or not
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				$item->length = $member['length'];
			}
		} else {
			die("Error:" . mysql_error());
		}
		
		$item->productdesc = $_POST['fibreproductdesc'] . " (" . $item->length . "M)";
		
		$qry = "SELECT price " .
				"FROM jomon_pricebreaks " .
				"WHERE productlengthid = $productlengthid " .
				"AND ((fromunit <= " . $item->qty . " " .
				"AND tounit >= " . $item->qty . " ) OR  " .
				"(fromunit <= " . $item->qty . " " .
				"AND tounit = 0))";
		$result = mysql_query($qry);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				$item->price = $member['price'];
			}
		} else {
			die("Error:" . mysql_error());
		}
		
	} else if ($_POST['type'] == "Labour Task") {
		$item->productlengthid = 0;
		$item->productdesc = $_POST['labourtaskproductdesc'];
		$item->qty = $_POST['labourtaskqty'];
		$item->productid = $_POST['labourtaskproduct'];
		$item->price = 0;
		$item->inout = $_POST['labourratehours'];
		$item->manneddays = $_POST['labourtaskqty'];
		$item->cat2 = $_POST['labourrateunit'];
		$technicianid = $_POST['labourtaskproduct'];
		
		$qry = "SELECT * " .
				"FROM jomon_technicianrates " .
				"WHERE id = $technicianid";
		$result = mysql_query($qry);
		
		//Check whether the query was successful or not
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				if ($_POST['labourratehours'] == "IN") {
					$item->price = $member['inhourrate'];
					
				} else {
					$item->price = $member['outhourrate'];
				}

				if ($_POST['labourrateunit'] == "1") {
					$item->price = $item->price / 7.5;
				}
			}
			
		} else {
			die("Error:" . mysql_error());
		}
		
	} else if ($_POST['type'] == "Bespoke") {
		$item->qty = 1;
		$item->price = $_POST['bespokeprice'];
		$item->productdesc = "Bespoke";
		$item->notes = $_POST['bespokenotes'];
		
	} else if ($_POST['type'] == "Emergency Charge") {
		$item->qty = 1;
		$item->productdesc = "Emergency Charge";
		
	} else if ($_POST['type'] == "Expedite Charge") {
		$item->qty = 1;
		$item->productdesc = "Expedite Charge";
		
	} else if ($_POST['type'] == "Sundry Items") {
		$item->productlengthid = 0;
		$item->productdesc = $_POST['ancillaryproductdesc'];
		$item->productid = $_POST['ancillaryproduct'];
		$item->cat2 = $_POST['ancillarycat1'];
		$item->qty = $_POST['ancillaryqty'];
		
		$qry = "SELECT * " .
				"FROM jomon_supplieditems " .
				"WHERE productid = $item->productid";
		$result = mysql_query($qry);
		
		//Check whether the query was successful or not
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				$item->price = $member['supplyinstalled'];
			}
			
		} else {
			die("Error:" . mysql_error());
		}
	}		
	
	$item->total = $item->qty * $item->price;
	
	if (! isset($_POST['itemnumber']) || $_POST['itemnumber'] == "") {
		$header->add($item);
	}

	if ($_POST['topleveltype'] == "Longlines" ||
	    $_POST['topleveltype'] == "Patchleads") {
	    	
		if (isset($_POST['from_areaid'])) {
			$counter = count($_POST['to_areaid']);
			$item->longline = new LongLineItem();
			
			for ($i = 0; $i < $counter; $i++) {
				$item->longline->item[$i] = new LongLineSubItem();
				$item->longline->item[$i]->fromarea = $_POST['from_areaid'][$i];
				$item->longline->item[$i]->fromcabinet = $_POST['from_cabinet'][$i];
				$item->longline->item[$i]->toarea = $_POST['to_areaid'][$i];
				$item->longline->item[$i]->tocabinet = $_POST['to_cabinet'][$i];
				$item->longline->item[$i]->notes = $_POST['to_presentation'][$i];
			}
		}
		
	} else if ($_POST['topleveltype'] == "Panel Link") {
		
		if (isset($_POST['from_areaid'])) {
			$counter = count($_POST['to_areaid']);
			
			$item->panel = new PanelItem();
			
			for ($i = 0; $i < $counter; $i++) {
				$item->panel->item[$i] = new PanelSubItem();
				$item->panel->item[$i]->fromarea = $_POST['from_areaid'][$i];
				$item->panel->item[$i]->fromcabinet = $_POST['from_cabinet'][$i];
				$item->panel->item[$i]->fromlocation = $_POST['from_location'][$i];
				$item->panel->item[$i]->fromulocation = $_POST['from_uloc'][$i];
				$item->panel->item[$i]->fromposition = $_POST['from_position'][$i];
				$item->panel->item[$i]->toarea = $_POST['to_areaid'][$i];
				$item->panel->item[$i]->tocabinet = $_POST['to_cabinet'][$i];
				$item->panel->item[$i]->tolocation = $_POST['to_location'][$i];
				$item->panel->item[$i]->toulocation = $_POST['to_uloc'][$i];
				$item->panel->item[$i]->toposition = $_POST['to_position'][$i];
			}
		}
	}
	
	$header->recalculateCharges();

	header("location: " . $_POST['callback']);	
?>
