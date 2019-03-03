<?php
	$dbHost = 'localhost';
	$dbUser = 'ctfadmin';
	$dbPass = 'a7u&09Tq&xLY60lbvPbJ';
	$db = '1337_Secur1ty';

	$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $db)
	or die("Error. Ping us so we can fix it.");

	$dbUser = 'updateadmin';
	$dbPass = 'c%L68TPZ!n!JOxezuKvR';
	$updateConn = mysqli_connect($dbHost, $dbUser, $dbPass, $db)
	or die(mysqli_connect_error());

	$dbUser = 'vulnadmin';
	$dbPass = 'ayKOMD13&o8@?!D0FkUB';
	$vulnConn = mysqli_connect($dbHost, $dbUser, $dbPass, $db)
	or die(mysqli_connect_error());
?>
