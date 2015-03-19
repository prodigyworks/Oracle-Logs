<?php 
	require_once("system-db.php");
	require_once("quotationitem.php");
	
	start_db();
	initialise_db();
	
	echo "[\n";
	echo "{\n";
		
	if (isset($_GET['item'])) {
		$header = $_SESSION['QUOTATION'];
		$item = $header->get($_GET['item']);
		
		echo "\"cat1\": \"" . $item->cat1 . "\",\n";
		echo "\"cat2\": \"" . $item->cat2 . "\",\n";
		echo "\"cat3\": \"" . $item->cat3 . "\",\n";
		echo "\"product\": \"" . $item->productid . "\",\n";
		echo "\"description\": \"" . $item->productdesc . "\",\n";
		echo "\"length\": \"" . $item->productlengthid . "\",\n";
		echo "\"qty\": \"" . $item->qty . "\",\n";
		echo "\"price\": \"" . $item->price . "\",\n";
		echo "\"notes\": \"" . escape_notes($item->notes) . "\",\n";
		echo "\"manneddays\": \"" . $item->manneddays . "\",\n";
		echo "\"labourratehours\": \"" . $item->inout . "\",\n";
					   	
	   	if ($item->longline != null) {
			echo "\"longline\": [\n";

			$counter = count($item->longline->item);

			for ($x = 0; $x < $counter; $x++) {
				if ($x > 0) {
					echo ",\n";
				}
				
				echo "{\n";
				echo "\"fromarea\": \"" . $item->longline->item[$x]->fromarea . "\",\n";
				echo "\"fromcabinet\": \"" . $item->longline->item[$x]->fromcabinet . "\",\n";
				echo "\"toarea\": \"" . $item->longline->item[$x]->toarea . "\",\n";
				echo "\"tocabinet\": \"" . $item->longline->item[$x]->tocabinet . "\",\n";
				echo "\"notes\": \"" . $item->longline->item[$x]->notes . "\"\n";
				echo "}";
			}
			
			echo "\n],\n";
	   	}
			
	
		if ($item->panel != null) {
			echo "\"panels\": [\n";
			
			$counter = count($item->panel->item);
			
			for ($x = 0; $x < $counter; $x++) {
				if ($x > 0) {
					echo ",\n";
				}
				
				echo "{\n";
				echo "\"fromarea\": \"" . $item->panel->item[$x]->fromarea . "\",\n";
				echo "\"fromcabinet\": \"" . $item->panel->item[$x]->fromcabinet . "\",\n";
				echo "\"fromposition\": \"" . $item->panel->item[$x]->fromposition . "\",\n";
				echo "\"fromlocation\": \"" . $item->panel->item[$x]->fromlocation . "\",\n";
				echo "\"fromulocation\": \"" . $item->panel->item[$x]->fromulocation . "\",\n";
				echo "\"toarea\": \"" . $item->panel->item[$x]->toarea . "\",\n";
				echo "\"tocabinet\": \"" . $item->panel->item[$x]->tocabinet . "\",\n";
				echo "\"toposition\": \"" . $item->panel->item[$x]->toposition . "\",\n";
				echo "\"tolocation\": \"" . $item->panel->item[$x]->tolocation . "\",\n";
				echo "\"toulocation\": \"" . $item->panel->item[$x]->toulocation . "\"\n";
				echo "}";
			}
			
			echo "\n],\n";
		}
	}
	
	echo "\"type\": \"" . $item->type . "\"";
	
	echo "\n}\n";
	echo "\n]\n";
	
?>
