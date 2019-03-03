<?php
	include 'sql_connect.php';
	global $vulnConn;
	include 'cookie_check.php';
	global $cookieCheck;
	include 'admin_check.php';
	global $adminCheck;

	if(!$cookieCheck){
		echo "ERROR: Need to sign in to a valid account to send a message.";
		header( "refresh:5;url=/" );
	} else if ($adminCheck) {
		header( "Location: /" );
	} else {
		if($_GET['id']){
			$id = $_GET['id'];
			$sql = "SELECT * FROM Messages WHERE MessageID = '$id'";
			$result = mysqli_query($vulnConn, $sql)
			or die("Brought to you by the 1337est of Secur1ty!!!");
			$row = mysqli_fetch_assoc($result);
			echo "<b>From:</b> " . $row['FirstName'] . " (" . $row['Username'] . ")";
			echo "<br>";
			$dateTime = explode(" ", $row['CreateDate']);
			$date = $dateTime[0];
			$datePieces = explode("-", $date);
			$date = $datePieces[1] . "/" . $datePieces[2] . "/" . substr($datePieces[0], 2, 2);
			$time = substr($dateTime[1], 0, -3);
			echo "<b>Date:</b> $date";
			echo "<br>";
			echo "<b>Time:</b> $time";
			echo "<br>";
			echo "<b>Message:</b> <br style='margin-bottom:10px;'>" ;
			echo $row['Message'];
			echo "<br><br>";
			echo "<button id='back'>Back</button>";

			echo "<script type='text/javascript'>";
	    			echo "document.getElementById('back').onclick = function () {";
					echo "location.href = '/';";
	   	 		echo "};";
			echo "</script>";
		}
	}
?>
