					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<div class="tail-middle">
			<div class="row-2">
				<div class="inside">
					<div class="carousel-box">
						<div class="prev"><a href="#"><img src="images/prev.png" alt="" /></a></div>
						<div class="next"><a href="#"><img src="images/next.png" alt="" /></a></div>
						<div class="carousel">
							<ul>
								<li>
									<div class="box">
										<div class="border-top">
											<div class="border-bot">
												<div class="left-top-corner">
													<div class="right-top-corner">
														<div class="right-bot-corner">
															<div class="left-bot-corner">
																<div class="img-box2"><img src="images/slide-img1.jpg" alt="" />
																	<div class="inner">
																		<h4>Featured Articles</h4>
																		<p>
<?php
	$qry = "SELECT A.id, A.title, DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, B.login " .
			"FROM ols_article A " .
			"INNER JOIN ols_members B " .
			"ON B.member_id = A.memberid " .
			"WHERE A.published = 'Y' " .
			"AND A.featured = 'Y' " .
			"ORDER BY A.id DESC " .
			"LIMIT 3";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			echo "<a href='viewarticle.php?id=". $member['id'] . "'>" . $member['createddate'] . " - "  . $member['title'] . "</a><br>";
		}
	} else {
		logError($qry . " = " . mysql_error());
	}
?>
																		</p>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</li>
								<li>
									<div class="box">
										<div class="border-top">
											<div class="border-bot">
												<div class="left-top-corner">
													<div class="right-top-corner">
														<div class="right-bot-corner">
															<div class="left-bot-corner">
																<div class="inner">
																	<div class="img-box2"><img src="images/slide-img2.jpg" alt="" />
																		<div class="inner">
																			<h4>Recent Articles</h4>
																		<p>
<?php
	$qry = "SELECT A.id, A.title, DATE_FORMAT(A.createddate, '%d/%m/%Y') AS createddate, B.login " .
			"FROM ols_article A " .
			"INNER JOIN ols_members B " .
			"ON B.member_id = A.memberid " .
			"WHERE A.published = 'Y' " .
			"ORDER BY A.id DESC " .
			"LIMIT 3";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			echo "<a href='viewarticle.php?id=". $member['id'] . "'>" . $member['createddate'] . " - "  . $member['title'] . "</a><br />";
		}
		
	} else {
		logError($qry . " = " . mysql_error());
	}
?>
																		
																		
																		</p>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</li>
								<li>
									<div class="box">
										<div class="border-top">
											<div class="border-bot">
												<div class="left-top-corner">
													<div class="right-top-corner">
														<div class="right-bot-corner">
															<div class="left-bot-corner">
																<div class="inner">
																	<div class="img-box2"><img src="images/slide-img3.jpg" alt="" />
																		<div class="inner">
																			<h4>Popular Articles</h4>
																		<p>
<?php
	$qry = "SELECT A.id, A.title, A.viewcount, B.login " .
			"FROM ols_article A " .
			"INNER JOIN ols_members B " .
			"ON B.member_id = A.memberid " .
			"WHERE A.published = 'Y' " .
			"ORDER BY A.viewcount DESC, createddate DESC " .
			"LIMIT 3";
	$result = mysql_query($qry);
	
	if ($result) {
		while (($member = mysql_fetch_assoc($result))) {
			echo "<a href='viewarticle.php?id=" . $member['id'] . "'>" . $member['title'] . " - <span class='views'>(" . $member['viewcount'] . " views)</span></a><br>";
		}
	} else {
		logError($qry . " = " . mysql_error());
	}
?>
																		</p>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</li>
							</ul>
						</div>
					</div>
					<div class='bottomad'>
						<?php showAdvert(3, 960, 100); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<!-- footer -->
	<div id="footer">
		
		<div class="footer">
		<div>
		<a href="terms.php">Terms and conditions</a> |
		<a href="contactus.php">Contact us</a>
		</div>
		
		Powered by ProdigyWorks!<br />
		</div>
	</div>
</div>
<div id="oops" class="modal">
	<h4>Oops! An error has occurred.<h4>
	<h5>Please contact the administrator.<h5>
</div>
<?php 
global $global_error;

if ($global_error) {
?>
<script>
	$(document).ready(
			function() {
				$("#oops").dialog({
						modal: true,
						autoOpen: true,
						title: "An error has occurred",
						width: 400,
						buttons: {
							Ok: function() {
								$(this).dialog("close");
							}
						}
					});
			}
		);
</script>
<?php 
}
?>
</body>
</html>
