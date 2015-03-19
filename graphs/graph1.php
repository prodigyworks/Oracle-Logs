<?php
	require_once("../libchart/libchart.php"); 
	require_once('../system-db.php');
	
	start_db();
	initialise_db();
	
	function getTotal($costcode) {
		$total = 0;
		
		$qry = "SELECT SUM(A.total) AS total " .
				"FROM jomon_quoteitem A " .
				"INNER JOIN jomon_quoteheader B " .
				"ON B.id = A.headerid " .
				"WHERE B.costcode = '$costcode' ";
				"AND B.status IN ('C', 'V', 'Q') ";
		$result = mysql_query($qry);
		
		if ($result) {
			while (($member = mysql_fetch_assoc($result))) {
				$total = $member['total'];
			}
			
		} else {
			die(mysql_error());
		}
		
		return $total;
	}
	
	$chart = new PieChart(1005, 550);
	
	$chart->getPlot()->getPalette()->setPieColor(array(
		new Color(255, 0, 0),
		new Color(127, 255, 255),
		new Color(0, 0, 255),
		new Color(0, 255, 0)
	));

	$dataSet = new XYDataSet();
	$dataSet->addPoint(new Point("CAPEX DEAL RELATED CCF", getTotal("CAPEXCCF")));
	$dataSet->addPoint(new Point("CAPEX - BESPOKE", getTotal("CAPEXBESPOKE")));
	$dataSet->addPoint(new Point("OPEX - BESPOKE", getTotal("OPEXBESPOKE")));
	$dataSet->addPoint(new Point("OPEX - Customer PO", getTotal("OPEXCUSTOMERPO")));
	
	$chart->setDataSet($dataSet);

	$chart->setTitle("Cost Code - Breakdown");
	$chart->render();
?>
