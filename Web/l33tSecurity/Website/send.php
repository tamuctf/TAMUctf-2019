<?php
	include 'sql_connect.php';
	global $conn;
	include 'cookie_check.php';
	global $cookieCheck;
	include 'admin_check.php';
	global $adminCheck;

	if(!isset($_COOKIE['userid']) || !isset($_COOKIE['secret']) || !$cookieCheck){
		echo "ERROR: Need to sign in to a valid account to send a message.";
		header( "refresh:5;url=/" );
	} else if ($adminCheck) {
		header( "Location: /" );
	} else{
?>
		<form id='sendMessage' action='send' method='POST' accept-charset='UTF-8'>
		To: <input type='text' name='sendTo' id='sendTo' required oninvalid="this.setCustomValidity('Please Enter Username')" oninput="this.setCustomValidity('')" maxlength='20'>
		<br>
		<p style='color:red' name='error' id='error'></p>
		Message:<br><textarea maxlength='10000' name='message' id='message' style='margin-top: 5px; resize: none' rows='15' cols='139' required oninvalid="this.setCustomValidity('Please Enter Message')" oninput="this.setCustomValidity('')"></textarea>
		<input type='Submit' name='Submit' value='Send' />
		</form>
<?php
		if(isset($_POST['Submit'])) {
			$sendTo = htmlspecialchars($_POST['sendTo'], ENT_QUOTES, 'UTF-8');
			$message = htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8');

			$sql = "SELECT Username FROM Users";
			$result = mysqli_query($conn, $sql)
			or die("Error. Ping us so we can fix it.");

			$usernameFound = false;
			while($row = mysqli_fetch_assoc($result)){
			if($row['Username'] == $sendTo){
					$usernameFound = true;
				}
			}

			if(!$usernameFound) {
				echo "<script>";
				echo "document.getElementById('error').innerHTML = '*That user does not exist!'";
				echo "</script>";
			} else {
				$stmt = $conn->prepare("SELECT Username, FirstName FROM Users WHERE Secret = ?");
				$stmt->bind_param('s', $_COOKIE['secret']);
				$stmt->execute()
				or die("Error. Ping us so we can fix it.");
				$result = $stmt->get_result()
				or die("Error. Ping us so we can fix it.");

				$row = mysqli_fetch_assoc($result);
				$firstName = $row['FirstName'];
				$username = $row['Username'];

				if($sendTo == $username) {
					echo "<script>";
					echo "document.getElementById('error').innerHTML = '*You can't message yourself!'";
					echo "</script>";
				} else {
					$stmt = $conn->prepare("INSERT INTO Messages(Username, FirstName, MessageTo, Message) VALUE (?, ?, ?, ?)");
					$stmt->bind_param('ssss', $username, $firstName, $sendTo, $message);
					$stmt->execute()
					or die("Error. Ping us so we can fix it.");
					header('Location: /');
				}
			}
		}
	}
?>
