<?php
	include("system-header.php"); 
	
	function startsWith($Haystack, $Needle){
	    // Recommended version, using strpos
	    return strpos($Haystack, $Needle) === 0;
	}
	
	function endsWith($haystack, $needle) {     
		$length = strlen($needle);     
		$start  = $length * -1; 
		//negative     
		return (substr($haystack, $start) === $needle); 
	} 
 
	class PriceItem {
	    // property declaration
	    public $from = 0;
	    public $to = 0;
	}
 
	class ProductLength {
	    // property declaration
	    public $length = 0;
	    public $longline = 0;
	}
    
	if (isset($_FILES['csvfile'])) {
		if ($_FILES["csvfile"]["error"] > 0) {
			echo "Error: " . $_FILES["csvfile"]["error"] . "<br />";
			
		} else {
		  	echo "Upload: " . $_FILES["csvfile"]["name"] . "<br />";
		  	echo "Type: " . $_FILES["csvfile"]["type"] . "<br />";
		  	echo "Size: " . ($_FILES["csvfile"]["size"] / 1024) . " Kb<br />";
		  	echo "Stored in: " . $_FILES["csvfile"]["tmp_name"] . "<br>";
		}
		
		$pricearray = array();
		$lengtharray = array();
		$longlinearray = array();
		$subcat1 = "";
		$subcat2 = "";
		$row = 1;
		
		if (($handle = fopen($_FILES['csvfile']['tmp_name'], "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		        $num = count($data);
		        echo "<p> $num fields in line $row: <br /></p>\n";
		        $row++;
		        
		        if ($data[0] == "Cat 6 Copper") {
		        	$category = "Panels";
		        	$subcat1 = "Copper Panels";
		        	$subcat2 = "Cat 6";
		        }
		        
		        if ($data[0] == "Cat 6") {
		        	$category = "Patch Leads";
		        	$subcat1 = "Copper";
		        	$subcat2 = "Cat 6";
		        }
		        
		        if ($data[0] == "Cat 5e") {
		        	$category = "Patch Leads";
		        	$subcat1 = "Copper";
		        	$subcat2 = "Cat 5e";
		        }
		        
		        if ($data[0] == "OS1") {
		        	$category = "Patch Leads";
		        	$subcat1 = "Fibre";
		        	$subcat2 = "OS1";
		        }
		        
		        if ($data[0] == "Fibre OM2") {
		        	$category = "Panels";
		        	$subcat1 = "Fibre Panels";
		        	$subcat2 = "OM2";
		        }
		        
		        if ($data[0] == "OM2") {
		        	$category = "Patch Leads";
		        	$subcat1 = "Fibre";
		        	$subcat2 = "OM2";
		        }
		        
		        if ($data[0] == "Cat 6A Copper") {
		        	$category = "Panels";
		        	$subcat1 = "Copper Panels";
		        	$subcat2 = "Cat 6A";
		        }
		        
		        if ($data[0] == "Cat 6A") {
		        	$category = "Patch Leads";
		        	$subcat1 = "Copper";
		        	$subcat2 = "Cat 6A";
		        }
		        
		        if ($data[0] == "Fibre OM3") {
		        	$category = "Panels";
		        	$subcat1 = "Fibre Panels";
		        	$subcat2 = "OM3";
		        }
		        
		        if ($data[0] == "OM3") {
		        	$category = "Patch Leads";
		        	$subcat1 = "Fibre";
		        	$subcat2 = "OM3";
		        }
		        
		        if ($data[7] != "") {
				        
			        if ($data[3] == "") {
						unset($pricearray);
						unset($lengtharray);
						unset($longlinearry);
						
						$pricearray = array();
						$lengtharray = array();
						$longlinearray = array();
			        }
						
			        if (startsWith($data[3], "Price")) {
				        for ($c=2; $c < $num; $c++) {
				        	$str = $data[$c];
				        	
				        	if ($str == "") {
				        		continue;
				        	}
				        	
			        		$item = new PriceItem(); 
			        		$hyphen = strpos($str, "-");
			        		
			        		if ($hyphen) {
			        			$item->from =  substr($str, 6, $hyphen - 6);
			        			$item->to =  substr($str, $hyphen + 1);
			        			
				        		echo "Price found column from " . substr($str, 6, $hyphen - 6). " to " . substr($str, $hyphen + 1) . "<br>";
				        		
			        		} else {
				        		$hyphen = strpos($str, "+");
			        			$item->from =  substr($str, 6, $hyphen - 6);
				        		
				        		echo "Price found column from " . substr($str, 6, $hyphen - 6) . "<br>";
			        		}
			        		
			        		$pricearray[$c - 2] = $item;
				        }
				        
			        } else if (endsWith($data[6], "Longline")) {
				        for ($c=2; $c < $num; $c++) {
				        	$str = $data[$c];
				        	
				        	if ($str == "") {
				        		$longlinearray[$c - 2] = "0";
				        		
				        	} else {
				        		$longlinearray[$c - 2] = "1";
				        	}
			        		echo "Long line found" . "<br>";
			        		
				        }
				        
			        } else if (endsWith($data[3], "M")) {
				        for ($c=2; $c < $num; $c++) {
				        	$str = $data[$c];
				        	
				        	if ($str == "") {
				        		continue;
				        	}
				        	
			        		$item = new ProductLength(); 
			        		$hyphen = strpos($str, "M");
			        		
		        			$item->length =  substr($str, 0, $hyphen );
		        			
		        			if (count($longlinearray) > ($c - 2)) {
			        			$item->longline =  $longlinearray[$c - 2];
		        			}
				        		
			        		echo "Length found column from " . $item->length . "<br>";
			        		
			        		$lengtharray[$c - 2] = $item;
				        }
						
			        } else if (startsWith($data[3], "£")) {
			        	

				        for ($c=2; $c < $num; $c++) {
				        	$str = $data[$c];
				        	
				        	if ($str == "") {
				        		continue;
				        	}
				        	
				        	if ($data[$c] == "-") {
				        		continue;
				        	}
				        	
				        	if ($category == "Patch Leads" || $category == "Long Lines") {
					            if ($lengtharray[$c - 2]->longline == "1") {
					            	$category = "Long Lines";
					            	
					            } else {
					            	$category = "Patch Leads";
					            }
				        	}
				        	
							$qry = "INSERT INTO jomon_categories (name, parentcategoryid, createdby, createddate) " .
									"VALUES " .
									"('$category', 0, " . $_SESSION['SESS_MEMBER_ID'] . ", NOW())";
							$result = mysql_query($qry);
				        	$categoryid =  mysql_insert_id();
	
							if (mysql_errno() == 1062) {
								$qry = "SELECT id " .
										"FROM jomon_categories " .
										"WHERE name = '$category'";
								$result = mysql_query($qry);
								
								//Check whether the query was successful or not
								if ($result) {
									while (($member = mysql_fetch_assoc($result))) {
										$categoryid = $member['id'];
									}
								}
								
							} else {
								if (! $result) die("Error :" . mysql_errno() . " : " .  mysql_error());
							}					
				        	
							$qry = "INSERT INTO jomon_categories (name, parentcategoryid, createdby, createddate) VALUES ('$subcat1', $categoryid, " . $_SESSION['SESS_MEMBER_ID'] . ", NOW())";
							$result = mysql_query($qry);
				        	$subcat1id =  mysql_insert_id();
				        	$productname = $data[1];
				        	
							if (mysql_errno() == 1062) {
								$qry = "SELECT id " .
										"FROM jomon_categories " .
										"WHERE name = '$subcat1' AND parentcategoryid = $categoryid";
								$result = mysql_query($qry);
								
								//Check whether the query was successful or not
								if ($result) {
									while (($member = mysql_fetch_assoc($result))) {
										$subcat1id = $member['id'];
									}
								}
								
							} else {
								if (! $result) die("Error :" . mysql_errno() . " : " .  mysql_error());
							}
				        	
							$qry = "INSERT INTO jomon_categories (name, parentcategoryid, createdby, createddate) VALUES ('$subcat2', $subcat1id, " . $_SESSION['SESS_MEMBER_ID'] . ", NOW())";
							$result = mysql_query($qry);
				        	$subcat2id =  mysql_insert_id();
				        	$productname = $data[1];
				        	
							if (mysql_errno() == 1062) {
								$qry = "SELECT id " .
										"FROM jomon_categories " .
										"WHERE name = '$subcat2' AND parentcategoryid = $subcat1id";
								$result = mysql_query($qry);
								
								//Check whether the query was successful or not
								if ($result) {
									while (($member = mysql_fetch_assoc($result))) {
										$subcat2id = $member['id'];
									}
								}
								
							} else {
								if (! $result) die("Error :" . mysql_errno() . " : " .  mysql_error());
							}
				        	
							$qry = "INSERT INTO jomon_products (name, categoryid, createdby, createddate) VALUES ('$productname', $subcat2id, " . $_SESSION['SESS_MEMBER_ID'] . ", NOW())";
							$result = mysql_query($qry);
				        	$productid =  mysql_insert_id();
				        	
							if (mysql_errno() == 1062) {
								$qry = "SELECT id " .
										"FROM jomon_products " .
										"WHERE categoryid = $subcat2id " .
										"AND name = '$productname'";
								$result = mysql_query($qry);
								
								//Check whether the query was successful or not
								if ($result) {
									while (($member = mysql_fetch_assoc($result))) {
										$productid = $member['id'];
									}
								}
								
							} else {
								if (! $result) die("Error :" . mysql_errno() . " : " .  mysql_error());
							}
				        	
				            $length = $lengtharray[$c - 2]->length ;
				            
							$qry = "INSERT INTO jomon_productlengths " .
									"(productid, length,  createdby, createddate) " .
									"VALUES " .
									"($productid, $length, " . $_SESSION['SESS_MEMBER_ID'] . ", NOW())";
							$result = mysql_query($qry);
				        	$productlengthid =  mysql_insert_id();
			        	
							if (mysql_errno() == 1062) {
								$qry = "SELECT id " .
										"FROM jomon_productlengths " .
										"WHERE productid = $productid " .
										"AND length = '$length'";
								$result = mysql_query($qry);
								
								//Check whether the query was successful or not
								if ($result) {
									while (($member = mysql_fetch_assoc($result))) {
										$productlengthid = $member['id'];
									}
								}
							
							} else {
								if (! $result) die("Error :" . mysql_errno() . " : " .  mysql_error());
							}
							
							if (count($pricearray) > 0) {
					            echo "Cat: " . $category . " Sub cat 1:" . $subcat1 . " Sub cat 2:" . $subcat2  . " Product: " . $data[1] . " Price: " . $data[$c] . " From: " . $pricearray[$c - 2]->from . " To: " . $pricearray[$c - 2]->to  .  " @length: " . $lengtharray[$c - 2]->length ."<br />\n";
					            
					            $fromunit = $pricearray[$c - 2]->from;
					            $tounit = $pricearray[$c - 2]->to;
					            $price = floatval(str_replace(",", "", substr($data[$c], 1)));
					            
								$qry = "INSERT INTO jomon_pricebreaks " .
										"(productlengthid, fromunit, tounit, price, createdby, createddate) " .
										"VALUES " .
										"($productlengthid, $fromunit, $tounit, $price, " . $_SESSION['SESS_MEMBER_ID'] . ", NOW())";
										
								$result = mysql_query($qry);
					        	$productbreakid =  mysql_insert_id();
			        	
								if (mysql_errno() == 1062) {
									$qry = "SELECT id " .
											"FROM jomon_pricebreaks " .
											"WHERE productlengthid = $productlengthid " .
											"AND fromunit = '$fromunit' " .
											"AND tounit = '$tounit'";
									$result = mysql_query($qry);
									
									//Check whether the query was successful or not
									if ($result) {
										while (($member = mysql_fetch_assoc($result))) {
											$pricebreakid = $member['id'];
										}
									}
									
									$qry = "UPDATE jomon_pricebreaks " .
											"SET price = $price " .
											"WHERE id = $pricebreakid";
									$result = mysql_query($qry);
								
									if (! $result) die("Error :" . mysql_errno() . " : " .  mysql_error());
									
								} else {
									if (! $result) die("Error :" . mysql_errno() . " : " .  mysql_error());
								}
					            
							} else {
					            echo "Cat: " . $subcat1 . " Sub cat:" . $subcat2  . " Product: " . $data[1] . " Price: " . $data[$c] . " @length: " . $lengtharray[$c - 2]->length ."<br />\n";
					            $fromunit = 0;
					            $tounit = 0;
					            $price = floatval(str_replace(",", "", substr($data[$c], 1)));
					            
								$qry = "INSERT INTO jomon_pricebreaks " .
										"(productlengthid, fromunit, tounit, price, createdby, createddate) " .
										"VALUES " .
										"($productlengthid, $fromunit, $tounit, $price, " . $_SESSION['SESS_MEMBER_ID'] . ", NOW())";
										
								$result = mysql_query($qry);
					        	$productbreakid =  mysql_insert_id();
			        	
								if (mysql_errno() == 1062) {
									$qry = "SELECT id " .
											"FROM jomon_pricebreaks " .
											"WHERE productlengthid = $productlengthid " .
											"AND fromunit = '$length' " .
											"AND tounit = '$tounit'";
									$result = mysql_query($qry);
									
									//Check whether the query was successful or not
									if ($result) {
										while (($member = mysql_fetch_assoc($result))) {
											$pricebreakid = $member['id'];
										}
									}
									
									$qry = "UPDATE jomon_pricebreaks " .
											"SET price = $price " .
											"WHERE id = $pricebreakid";
									$result = mysql_query($qry);
								
									if (! $result) die("Error :" . mysql_errno() . " : " .  mysql_error());
									
								} else {
									if (! $result) die("Error :" . mysql_errno() . " : " .  mysql_error());
								}
							}
				        }
			        }
			        
		        } else if (endsWith($data[0], "Technician")) {
		            $name = $data[0];
		            $inhourrate = floatval(str_replace(",", "", substr($data[1], 1)));
		            $outhourrate = floatval(str_replace(",", "", substr($data[3], 1)));
		            $sathourrate = floatval(str_replace(",", "", substr($data[2], 1)));
	
		            echo "Technician: " . $name . " Rates (" . $inhourrate  . ", " . $outhourrate . ", " . $sathourrate . ")<br />\n";
		            
					$qry = "INSERT INTO jomon_technicianrates " .
							"(name, inhourrate, outhourrate, sathourrate, createdby, createddate) " .
							"VALUES " .
							"('$name', $inhourrate, $outhourrate, $sathourrate, " . $_SESSION['SESS_MEMBER_ID'] . ", NOW())";
							
					$result = mysql_query($qry);
		        	$techid =  mysql_insert_id();
        	
					if (mysql_errno() == 1062) {
						$qry = "SELECT id " .
								"FROM jomon_technicianrates " .
								"WHERE name = '$name'";
						$result = mysql_query($qry);
						
						//Check whether the query was successful or not
						if ($result) {
							while (($member = mysql_fetch_assoc($result))) {
								$techid = $member['id'];
							}
						}
						
						$qry = "UPDATE jomon_technicianrates SET " .
								"inhourrate = $inhourrate, " .
								"outhourrate = $outhourrate, " .
								"sathourrate = $sathourrate " .
								"WHERE id = $techid";
						$result = mysql_query($qry);
					
						if (! $result) die("Error :" . mysql_errno() . " : " .  mysql_error());
						
					} else {
						if (! $result) die("Error :" . mysql_errno() . " : " .  mysql_error());
					}
		        }
		    }
		    
		    fclose($handle);
		}
		echo "<h1>" . $row . " downloaded</h1>";
	} else {
?>	
		
<form class="contentform" method="post" enctype="multipart/form-data" onsubmit="return askPassword()">
	<label>Upload CSV file </label>
	<input type="file" name="csvfile" id="csvfile" /> 
	
	<br />
	<div id="submit" class="show">
		<input type="submit" value="Upload" />
	</div>
</form>
<?php
	}
	
	include("system-footer.php"); 
?>