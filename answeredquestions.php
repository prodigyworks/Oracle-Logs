<?php
	require_once("questiontemplate.php");

	question(
			"WHERE A.published = 'Y' " .
			"AND (SELECT COUNT(*) FROM ols_questionanswers X  WHERE X.questionid = A.id AND published = 'Y') > 0 "
		);
?>
