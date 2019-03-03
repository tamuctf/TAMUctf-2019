<?php
	setcookie('userid', '', time() - 3600);
	setcookie('secret', '', time() - 3600);
	header("Location: /");
?>
