<?php
	include 'sql_connect.php';
	global $conn;

	$sql = "SELECT UserID, Secret FROM Users WHERE Username = '1337-admin'";
	$result = mysqli_query($conn, $sql)
	or die("Error. Ping us so we can fix it.");

	$row = mysqli_fetch_assoc($result);
	$userid1 = $row['UserID'];
	$secret1 = $row['Secret'];

	$stmt = $conn->prepare("SELECT UserID, Secret FROM Users WHERE UserID = ?");
	$stmt->bind_param('s', $_COOKIE['userid']);
	$stmt->execute()
	or die("Error. Ping us so we can fix it.");
	$result = $stmt->get_result()
	or die("Error. Ping us so we can fix it.");

	$row = mysqli_fetch_assoc($result);
	$userid2 = $row['UserID'];
	$secret2 = $row['Secret'];

	$stmt = $conn->prepare("SELECT UserID, Secret FROM Users WHERE SECRET = ?");
	$stmt->bind_param('s', $_COOKIE['secret']);
	$stmt->execute()
	or die("Error. Ping us so we can fix it.");
	$result = $stmt->get_result()
	or die("Error. Ping us so we can fix it.");

	$row = mysqli_fetch_assoc($result);
	$userid3 = $row['UserID'];
	$secret3 = $row['Secret'];

	$adminCheck;
	if($userid1 == $userid2 && $userid1 == $userid3 && $userid2 == $userid3 && $secret1 == $secret2 && $secret1 == $secret3 && $secret2 == $secret3 ){
		$adminCheck = true;
	} else {
		$adminCheck = false;
	}
?>
