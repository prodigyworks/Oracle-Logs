<?php
	//Include database connection details
	require_once('system-db.php');
	
	start_db();
	initialise_db();
	
	$header = $_SESSION['QUOTATION'];
	$counter = count($_POST['toPosition']);
	
	$item = $header->get($_POST['itemIndex']);
	$item->panel = new PanelItem();
	$item->panel->from = new PanelSubItemItem();
	$item->panel->from->area = $_POST['fromArea'];
	$item->panel->from->cabinet = $_POST['fromCabinet'];
	$item->panel->from->location = $_POST['fromLocation'];
	$item->panel->from->ulocation = $_POST['fromULocation'];
	$item->panel->from->position = $_POST['fromPosition'];
	
	for ($i = 0; $i < $counter; $i++) {
		$item->panel->to[$i] = new PanelSubItemItem();
		$item->panel->to[$i]->area = $_POST['toArea'][$i];
		$item->panel->to[$i]->cabinet = $_POST['toCabinet'][$i];
		$item->panel->to[$i]->location = $_POST['toLocation'][$i];
		$item->panel->to[$i]->ulocation = $_POST['toULocation'][$i];
		$item->panel->to[$i]->position = $_POST['toPosition'][$i];
	}
	
	//Check whether the query was successful or not
	echo "[\n";
	echo "{\"id\": \"0\", \"name\": \"" . count($_POST['toPosition']) . "\"}";
	
	echo "\n]\n";
?>