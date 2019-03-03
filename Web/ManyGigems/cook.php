
<?php
        $cookie_name = "gigem_continue";
	$cookie_value = base64_encode("cookies}");
	$path = "/cook.php";
	$time = 86400 / 24; // 1 hour
        setcookie($cookie_name, $cookie_value, time() + $time, $path);
?>
