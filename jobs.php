
<?php
	include("system-header.php"); 
	
	$data = getSiteConfigData();
	
	$fromrow = 0;
	$torow = 20;
	
	if (isset($_GET['from'])) {
		$fromrow = $_GET['from'];
	}
	
	if (isset($_GET['to'])) {
		$torow = $_GET['to'];
	}
	
	$pagesize = ($torow - $fromrow);
?>
<script type='text/javascript' src='jsc/jquery.autocomplete.js'></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places" type="text/javascript"></script>
<script src="http://www.google.com/uds/api?file=uds.js&v=1.0" type="text/javascript"><;/script>
<script src="http://maps.google.com/maps/api/js?v=3.1&sensor=false&region=PH"></script>
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $data->googlemapv2apikey; ?>" type="text/javascript"></script>
<script type="text/javascript">
	var directionsService = new google.maps.DirectionsService();
	
	function getLatLng(address)  {
	    var geocoder = new google.maps.Geocoder();
	    
	    geocoder.geocode(
	    		{ 
	    			'address' : address 
	    		}, 
	    		function( results, status ) {
			        if (status == google.maps.GeocoderStatus.OK ) {
						$('#lat').val(results[0].geometry.location.lat())
						$('#lng').val(results[0].geometry.location.lng())
						
			        } else {
			            alert( "Geocode was not successful for the following reason: " + status );
			        }
			    }
			);            
	}
	
	$(document).ready(function() {
			$("#location").change(function() {
					setTimeout(
							function() { 
								getLatLng($("#location").val());
							},
							500
						);
							
					
				});
		});
	
	function initialize() {
	    var input = document.getElementById('location');
        var options = {
        		types: ['(cities)'],           
        		componentRestrictions: {country: ["uk"]}       
        	};

	    var autocomplete = new google.maps.places.Autocomplete(input, options);
	}
	
	function search() {
		setTimeout( 
				function() { 
					$('#jobform').submit(); 
				}, 
				500
			);
	}
	
	google.maps.event.addDomListener(window, 'load', initialize);
</script>
<form method="post" id="jobform">
	<div id="jobsearch">
		<label>Location</label>
		<input type="text" id="location" name="location" class="textbox90" value="<?php if (isset($_POST['location'])) echo $_POST['location']; ?>" />
		
		<label>Keyword</label>
		<input type="text" id="keyword" name="keyword" class="textbox90" value="<?php if (isset($_POST['keyword'])) echo $_POST['keyword']; ?>" />
		
		<label>Job Type</label>
		<SELECT id="jobtype" name="jobtype">
			<OPTION value="">Both</OPTION>
			<OPTION value="C">Contract</OPTION>
			<OPTION value="P">Permanent</OPTION>
		</SELECT>
		
		<br>
		<br>
		<input type="hidden" id="search" name="search" value="X" />
		<input type="hidden" id="lat" name="lat" value="<?php if (isset($_POST['lat'])) echo $_POST['lat']; ?>" />
		<input type="hidden" id="lng" name="lng" value="<?php if (isset($_POST['lng'])) echo $_POST['lng']; ?>" />
		
	  	<span class="wrapper"><a class='link1' href="javascript: search();"><em><b>Search</b></em></a></span>
	  	<br>
	  	<br>
	  	<hr>
	</div>
</form>
<div class='jobs'>
	<table cellspacing=10 width=100% class='fixed' id="xx" cellspacing=0 cellpadding=0>
	    <tbody>
	    	<?php
	    		$row = 1;
	    		$nextpage = false;
	    		$prevpage = ($fromrow > 0);
	    		$endrow = $torow + 1;
	    		
	    		if (isset($_POST['search'])) {
	    			$where = " ";
	    			
					$qry = "SELECT B.firstname, B.website, B.lastname, B.imageid, A.*, " .
					       "DATE_FORMAT(A.createddate, '%d/%m/%Y') AS posteddate ";
	    		
		    		if (isset($_POST['jobtype']) && $_POST['jobtype'] != "") {
		    			$where = $where . " AND A.jobtype = '" . $_POST['jobtype'] . "'";
		    		}
	    		
		    		if (isset($_POST['keyword']) && $_POST['keyword'] != "") {
		    			$where = $where . "  AND MATCH (A.title, A.description, A.reference) AGAINST('" .$_POST['keyword'] . "' IN BOOLEAN MODE) ";
		    		}
	    		
		    		if (isset($_POST['lng']) && $_POST['lng'] != "") {
						$lng = $_POST['lng'];
						$lat = $_POST['lat'];
						
						$qry = $qry . ", ((ACOS(SIN($lat * PI() / 180) * SIN(A.lat * PI() / 180) + COS($lat * PI() / 180) * COS(A.lat * PI() / 180) * COS(($lng - A.lng) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance ";
						$where = $where . " ORDER BY distance ";
		    		}
		    		
					$qry = $qry .
					   " FROM ols_job A " .
					   "INNER JOIN ols_members B " .
					   "ON B.member_id = A.memberid " .
					   "WHERE A.status = 'O' " .
					   $where .
	    			   "LIMIT $fromrow, $endrow";
					$result = mysql_query($qry);
					
					if ($result) {
						while (($member = mysql_fetch_assoc($result))) {
							if ($row++ > ($pagesize)) {
								$nextpage = true;
								break;
							};
							echo "<tr>";
							echo "<td class='label'>&nbsp</td>";
							echo "<td>&nbsp;</td>";
							echo "</tr>\n";
							
							echo "<tr>";
							echo "<td colspan=2 class='title'><a href='jobdetail.php?id=" . $member['id'] . "'>" . $member['title'] . "</a></td>";
							echo "</tr>\n";
							
							if (isset($member['distance'])) {
								echo "<tr>";
								echo "<td class='label'>Distance</td>";
								echo "<td>" . number_format($member['distance'], 1) . " miles</td>";
								echo "</tr>\n";
							}
							
							echo "<tr>";
							echo "<td class='label'>Location</td>";
							echo "<td>" . $member['location'] . "</td>";
							echo "</tr>\n";
							
							echo "<tr>";
							echo "<td class='label'>Recruitment Agent</td>";
							echo "<td><a href='" . $member['website'] . "'>" . $member['firstname'] . " " . $member['lastname'];

							if ($member['imageid'] != null && $member['imageid'] != 0) {						
								echo "</a>&nbsp;<img src='system-imageviewer.php?id=" . $member['imageid'] . "' height=16 /></td>";
							}
							
							echo "</tr>\n";
							
							echo "<tr>";
							echo "<td class='label'>Reference</td>";
							echo "<td>" . $member['reference'] . "</td>";
							echo "</tr>\n";
							
							echo "<tr>";
							echo "<td class='label'>Posted</td>";
							echo "<td>" . $member['posteddate'] . "</td>";
							echo "</tr>\n";
							
							echo "<tr>";
							echo "<td class='label'>Type</td>";
							echo "<td>" . ($member['jobtype'] == "P" ? "Permanent" : "Contract") . "</td>";
							echo "</tr>\n";
							
							echo "<tr>";
							echo "<td class='label'>Currency</td>";
							echo "<td>" . $member['currency'] . "</td>";
							echo "</tr>\n";
							
							if ($member['rateper'] && $member['rateper'] != "") {
								echo "<tr>";
								echo "<td class='label'>Rate Per</td>";
								echo "<td>";
								?>
								<SELECT id="rateper" name="rateper" class="hidden" value="<?php echo $member['rateper']; ?>">
									<OPTION value="HOUR">Hour</OPTION>
									<OPTION value="WEEK">Week</OPTION>
									<OPTION value="MONTH">Month</OPTION>
									<OPTION value="YEAR">Year</OPTION>
								</SELECT>
								<DIV class="textvalue"></DIV>
								<?php
								echo "</tr>\n";
							}
							
							if ($member['rate'] && $member['rate'] != "") {
								echo "<tr>";
								echo "<td class='label'>Rate</td>";
								echo "<td>";
								?>
								<SELECT id="rate" name="rate" class="hidden" value="<?php echo $member['rate']; ?>">
									<OPTION value="ZEROTOTEN">0 - 10</OPTION>
									<OPTION value="TENTOTWENTY">10 - 20</OPTION>
									<OPTION value="TWENTYTOTHIRTY">20 - 30</OPTION>
									<OPTION value="THIRTYTOFORTY">30 - 40</OPTION>
									<OPTION value="FORTYTOFIFTY">40 - 50</OPTION>
									<OPTION value="FIFTYTOHUNDRED">50 - 100</OPTION>
									<OPTION value="HUNDREDPLUS">100+</OPTION>
								</SELECT>
								<DIV class="textvalue"></DIV>
								<?php
								echo "</td>";
								echo "</tr>\n";
							}
							
							if ($member['salary'] && $member['salary'] != "") {
								echo "<tr>";
								echo "<td class='label'>Salary Range</td>";
								echo "<td>";
								?>
								<SELECT id="salary" name="salary" class="hidden" value="<?php echo $member['salary']; ?>">
									<OPTION value="ZEROTOTEN">0 - 10,000</OPTION>
									<OPTION value="TENTOTWENTY">10,000 - 20,000</OPTION>
									<OPTION value="TWENTYTOTHIRTY">20,000 - 30,000</OPTION>
									<OPTION value="THIRTYTOFORTY">30,000 - 40,000</OPTION>
									<OPTION value="FORTYTOFIFTY">40,000 - 50,000</OPTION>
									<OPTION value="FIFTYTOHUNDRED">50,000 - 100,000</OPTION>
									<OPTION value="HUNDREDPLUS">100,000+</OPTION>
								</SELECT>
								<DIV class="textvalue"></DIV>
								<?php
								echo "</td>";
								echo "</tr>\n";
							}
							
							echo "<tr>";
							echo "<td colspan=2><div class='restrictheight'>" . $member['description'] . "</div><br><a href='jobdetail.php?id=" . $member['id'] . "'>More</a></td>";
							echo "</tr>\n";
							
							echo "<tr>";
							echo "<td colspan=2><hr></td>";
							echo "</tr>\n";
						}
					} else {
						logError($qry . " = " . mysql_error());
					}
	    		}
	    	?>
	    </tbody>
	</table>
	
	<?php
		if ($prevpage) {
			echo "<img src='images/previouspage.png' onclick='window.location.href = \"" . $_SERVER['PHP_SELF'] . "?from=" . ($fromrow - $pagesize) . "&to=" . ($torow - $pagesize) . "\"' />";
		}
		
		if ($nextpage) {
			echo "<img src='images/nextpage.png' onclick='window.location.href = \"" . $_SERVER['PHP_SELF'] . "?from=" . ($fromrow + $pagesize) . "&to=" . ($torow + $pagesize) . "\"' />";
		}
	?>
</div>
<script>
	$(document).ready(function() {
			$(".textvalue").each(function() {
					$(this).prev().val($(this).prev().attr("value"));
					$(this).html($(this).prev().find("option:selected").text());
				});
		});
</script>
<?php
	include("system-footer.php"); 
?>