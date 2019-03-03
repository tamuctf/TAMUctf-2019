<?php
	include 'sql_connect.php';
	include 'auth.php';

	if(isset($_POST['Submit'])) {
		Login();
	}

	function Login() {
		$username = $_POST['username'];
		$password = $_POST['password'];

		if(!CheckLoginDB($username,$password)){
			header('Location: /');
			return false;
		}
	}
	
	function CheckLoginDB($username, $password) {
		global $conn;
		$pwdHash = md5($password);

		$stmt = $conn->prepare("SELECT * FROM Users WHERE Username = ? AND Password = ?");
		$stmt->bind_param('ss', $username, $pwdHash);
		$stmt->execute()
		or die("Error. Ping us so we can fix it.");
		$result = $stmt->get_result()
		or die("Error. Ping us so we can fix it.");
		$row = mysqli_fetch_assoc($result);

		$ga = new PHPGangsta_GoogleAuthenticator();
		$checkResult = $ga->verifyCode($row['Secret'], $_POST['totp'], 2);    // 2 = 2*30sec clock tolerance

		if(!empty($row['UserID']) && !empty($row['Secret']) && $checkResult) {
			setcookie('userid', $row['UserID'], false, '/');
			setcookie('secret', $row['Secret'], false, '/');
			header('Location: /');
		}
		else{	
			return false;
		}
	}
?>
