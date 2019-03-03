<?php
	include 'sql_connect.php';
	global $updateConn;
	include 'cookie_check.php';
	global $cookieCheck;
	$nameChange = false;
	include 'admin_check.php';
	global $adminCheck;

	if(!$cookieCheck){
		echo "ERROR: Need to sign in to a valid account.";
		header( "refresh:5;url=/" );
	} else if ($adminCheck) {
		header( "Location: /" );
	} else {
		if(!empty($_POST['firstname'])){
			$firstname = htmlspecialchars($_POST['firstname'], ENT_QUOTES, 'UTF-8');
			$stmt = $updateConn->prepare("UPDATE Users SET FirstName = ? WHERE UserID = ?");
			$stmt->bind_param('ss', $firstname, $_COOKIE['userid']);
			$stmt->execute()
			or die("Error. Ping us so we can fix it.");

			$stmt = $conn->prepare("SELECT Username FROM Users WHERE UserID = ?");
			$stmt->bind_param("s", $_COOKIE['userid']);
			$stmt->execute()
			or die("Error. Ping us so we can fix it.");
			$result = $stmt->get_result()
			or die("Error. Ping us so we can fix it.");
			$row = mysqli_fetch_assoc($result);

			$stmt = $updateConn->prepare("UPDATE Messages SET FirstName = ? WHERE Username = ?");
			$stmt->bind_param("ss", $firstname, $row['Username']);
			$stmt->execute()
			or die("Error. Ping us so we can fix it.");

			$sql = "UPDATE Messages SET FirstName = '" . $firstname . "' WHERE Username = '" . $row['Username'] . "'";
			mysqli_query($conn, $sql);

			$nameChange = true;
		}

		if(!empty($_POST['lastname'])){
			$lastname = htmlspecialchars($_POST['lastname'], ENT_QUOTES, 'UTF-8');
			$stmt = $updateConn->prepare("UPDATE Users SET LastName = ? WHERE UserID = ?");
			$stmt->bind_param("ss", $lastname, $_COOKIE['userid']);
			$stmt->execute()
			or die("Error. Ping us so we can fix it.");

			$nameChange = true;
		}

		if(!empty($_POST['phone'])){
			$phone = htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');
			$stmt = $updateConn->prepare("UPDATE Users SET Phone = ? WHERE UserID = ?");
			$stmt->bind_param("ss", $phone, $_COOKIE['userid']);
			$stmt->execute()
			or die("Error. Ping us so we can fix it.");
		} else {
			$stmt = $updateConn->prepare("UPDATE Users SET Phone = '' WHERE UserID = ?");
			$stmt->bind_param("s", $_COOKIE['userid']);
			$stmt->execute()
			or die("Error. Ping us so we can fix it.");
		}

		if(!empty($_POST['password'])){
			$pwdHash = md5($_POST['password']);

			$stmt = $updateConn->prepare("UPDATE Users SET Password = ? WHERE UserID = ?");
			$stmt->bind_param("ss", $pwdHash, $_COOKIE['userid']);
			$stmt->execute()
			or die("Error. Ping us so we can fix it.");
		}

		if(!empty($_POST['description'])){
			$description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
			$stmt = $updateConn->prepare("UPDATE Users SET Description = ? WHERE UserID = ?");
			$stmt->bind_param("ss", $description, $_COOKIE['userid']);
			$stmt->execute()
			or die("Error. Ping us so we can fix it.");
		} else {
			$stmt = $updateConn->prepare("UPDATE Users SET Description = '' WHERE UserID = ?");
			$stmt->bind_param("s", $_COOKIE['userid']);
			$stmt->execute()
			or die("Error. Ping us so we can fix it.");
		}

		if($nameChange){
			$stmt = $conn->prepare("SELECT Username, FirstName, LastName FROM Users WHERE UserID = ?");
			$stmt->bind_param("s", $_COOKIE['userid']);
			$stmt->execute()
			or die("Error. Ping us so we can fix it.");
			$result = $stmt->get_result()
			or die("Error. Ping us so we can fix it.");

			$row = mysqli_fetch_assoc($result);
			$originalUsername = $row['Username'];
			$username = strtolower(substr($row['FirstName'], 0, 1)) . strtolower($row['LastName']);
			if($username != $originalUsername){
				$checkingUsername = true;
				$counter = 0;
				while($checkingUsername){
					$sql = "SELECT Username FROM Users";
					$result = mysqli_query($conn, $sql);
					$checkingUsername = false;
					while($row = mysqli_fetch_assoc($result)) {
						if($row['Username'] == $username){
							if($counter > 0) {
								$username = substr($username, 0, strlen($username) - strlen((string)$counter));
							}
							$username = $username . $counter;
							$counter++;
							$checkingUsername = true;
						}
					}
					echo $username;
				}
			}
			$email = $username . "@tamu.edu";

			$stmt = $updateConn->prepare("UPDATE Users SET Username = ?, Email = ? WHERE UserID = ?");
			$stmt->bind_param("sss", $username, $email, $_COOKIE['userid']);
			$stmt->execute()
			or die("Error. Ping us so we can fix it.");

			$stmt = $updateConn->prepare("UPDATE Messages SET Username = ? WHERE Username = ?");
			$stmt->bind_param("ss", $username, $originalUsername);
			$stmt->execute()
			or die("Error. Ping us so we can fix it.");

			$stmt = $updateConn->prepare("UPDATE Messages SET MessageTo = ? WHERE MessageTo = ?");
			$stmt->bind_param("ss", $username, $originalUsername);
			$stmt->execute()
			or die("Error. Ping us so we can fix it.");
		}
		header('Location: /');
	}
?>
