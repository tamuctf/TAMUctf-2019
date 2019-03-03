<?php
	include 'sql_connect.php';
	include 'auth.php';

	if(isset($_POST['Submit'])) {
		Register();
	}

	function Register() {
		global $conn;
		$firstname = htmlspecialchars($_POST['firstname'], ENT_QUOTES, 'UTF-8');
		$lastname = htmlspecialchars($_POST['lastname'], ENT_QUOTES, 'UTF-8');
		$username = strtolower(substr($firstname, 0, 1)).strtolower($lastname);
		$checkingUsername = true;
		$counter = 0;
		$badUsername = false;
		while($checkingUsername){
			$sql = "SELECT Username FROM Users";
			$result = mysqli_query($conn, $sql);
			$checkingUsername = false;
			while($row = mysqli_fetch_assoc($result)) {
				if($row['Username'] == $username && $counter == 1000) {
					$badUsername = true;
					break;
				}
				else if($row['Username'] == $username){
					if($counter > 0) {
						$username = substr($username, 0, strlen($username) - strlen((string)$counter));
					}
					$username = $username . $counter;
					$counter++;
					$checkingUsername = true;
				}
			}
			if($badUsername) {
				break;
			}
		}
		if($badUsername){
			echo "ERROR: Unfortunately we have run out of available usernames for that first name and last name combination. Please use a first name with a different letter as the first character or use a different last name.";
			header( "refresh:10;url=/" );
		} else {
			$password = $_POST['password'];
			$pwdHash = md5($password);
			$ga = new PHPGangsta_GoogleAuthenticator();
			$secret = $ga->createSecret();
			$checkingSecret = true;

			while($checkingSecret){
				$sql = "SELECT Secret FROM Users";
				$result = mysqli_query($conn, $sql);
				$checkingSecret = false;
				while($row = mysqli_fetch_assoc($result)){
					if($secret == $row['Secret']){
						$secret = $ga->createSecret();
						$checkingSecret = true;
					}
				}
			}

			$phone;
			if(empty($_POST['phone'])){
				$phone = "";
			} else {
				$phone = htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');
			}
			$description = "";
			$email = $username . "@1337secur1ty.hak";

			$stmt = $conn->prepare("INSERT INTO Users(Username, Password, FirstName, LastName, Phone, Email, Description, Secret) VALUE (?, ?, ?, ?, ?, ?, ?, ?)");
			$stmt->bind_param('ssssssss', $username, $pwdHash, $firstname, $lastname, $phone, $email, $description, $secret);

			$stmt->execute()
			or die("Error. Ping us so we can fix it.");

			$stmt = $conn->prepare("SELECT UserID FROM Users WHERE SECRET = ?");
			$stmt->bind_param("s", $secret);
			$stmt->execute()
			or die("Error. Ping us so we can fix it.");
			$result = $stmt->get_result()
			or die("Error. Ping us so we can fix it.");

			$row = mysqli_fetch_assoc($result);
			setcookie('userid', $row['UserID'], false, '/');
			setcookie('secret', $secret, false, '/');

			$stmt = $conn->prepare("INSERT INTO Messages(Username, FirstName, MessageTo, Message) VALUE ('1337-admin', 'Joe', ?, 'Welcome to 1337 Secur1ty, the family that you want to work at 16 hours a day. Because here at 1337 Secur1ty, we care!')");
			$stmt->bind_param('s', $username);
			$stmt->execute()
			or die("Error. Ping us so we can fix it.");

			header('Location: /');
			return true;
		}
	}
?>
