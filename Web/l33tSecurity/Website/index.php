<html>
<head>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<?php
		include 'sql_connect.php';
		include 'auth.php';
		global $conn;
		include 'cookie_check.php';
		global $cookieCheck;
		include 'admin_check.php';
		global $adminCheck;
	?>
	<div class="tab">
		<?php
			if(!isset($_COOKIE['userid']) || !isset($_COOKIE['secret']) || !$cookieCheck) {
		?>
			<button class="tablinks" onclick="openCity(event, 'Login')" id="defaultOpen">Login</button>
  			<button class="tablinks" onclick="openCity(event, 'Register')">Register</button>
		<?php
			}
			if(isset($_COOKIE['userid']) && isset($_COOKIE['secret']) && $cookieCheck) {
		?>
			<button class="tablinks" onclick="openCity(event, 'Profile')" id="defaultOpen">Profile</button>
			<?php if(!$adminCheck){ ?>
			<button class="tablinks" onclick="openCity(event, 'Messages')">Messages</button>
			<button class="tablinks" onclick="openCity(event, 'Employees')">Employees</button>
			<?php } ?>
			<form id='logout' action='logout.php' method='POST' accept-charset='UTF-8'>
				<input type='hidden' name='submitted' id='submitted' value='1' />
				<button class="tablinks" style="float: right"type='Submit' name='Submit' />Logout</button>
			</form>
		<?php
			}
		?>
	</div>
	<?php
		if(!isset($_COOKIE['userid']) || !isset($_COOKIE['secret']) || !$cookieCheck) {
	?>
	<div id="Login" class="tabcontent">
		<form id='login' action='login' method='POST' accept-charset='UTF-8'>
			<fieldset style="width:25%">
				<legend>Login</legend>

				<label for='username'> Username:</label>
				<br>
				<input type='text' name='username' id='username' maxlength="20" required oninvalid="this.setCustomValidity('Please Enter Username')" oninput="this.setCustomValidity('')"/>

				<br><br>
				<label for='password'>Password:</label>
				<br>
				<input type='password' name='password' id='password' maxlength="50" required oninvalid="this.setCustomValidity('Please Enter Password')" oninput="this.setCustomValidity('')"/>
				<br><br>

				<label for='totp'>TOTP Code:</label>
				<br>
				<input type='password' name='totp' id='totp' maxlength="6" required oninvalid="this.setCustomValidity('Please Enter TOTP Code')" oninput="this.setCustomValidity('')"/>
				<br><br>

				<input type='Submit' name='Submit' value='Login' />
			</fieldset>
		</form>
	</div>
	<div id="Register" class="tabcontent">
 		<form id='register-user' action='register_user' method='POST' accept-charset='UTF-8'>
			<fieldset style="width:25%">
				<legend>Register User</legend>

				<label for='firstname'>First Name:</label>
				<br>
				<input type='text' name='firstname' id='firstname' maxlength="10" pattern = "[a-zA-Z]+" required oninvalid="this.setCustomValidity('Please Enter First Name')" oninput="this.setCustomValidity('')"/>

				<br><br>
				<label for='lastname'>Last Name:</label>
				<br>
				<input type='text' name='lastname' id='lastname' maxlength="15" pattern = "[a-zA-Z]+" required oninvalid="this.setCustomValidity('Please Enter Last Name')" oninput="this.setCustomValidity('')"/>

				<br><br>
				<label for='password'>Password:</label>
				<br>
				<input type='password' name='password' id='password' maxlength="50" required oninvalid="this.setCustomValidity('Please Enter Password')" oninput="this.setCustomValidity('')"/>

				<br><br>
				<label for='password'>Phone Number:</label>
				<br>
				<input type="tel" id="phone" name="phone" pattern="[0-9]{10}" oninvalid="this.setCustomValidity('Please Enter Correct Phone # - ex. 1234567891')" oninput="this.setCustomValidity('')">

				<br><br>
				<input type='Submit' name='Submit' value="Register"/>
			</fieldset>
			<b>*** UPON REGISTRATION, ADD QR-CODE TO GOOGLE AUTHENTICATOR APP! ***</b>
		</form>
	</div>
	<?php
		}
		if(isset($_COOKIE['userid']) && isset($_COOKIE['secret']) && $cookieCheck) {
	?>
	<div id="Profile" class="tabcontent">
		<?php
			$stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
			$stmt->bind_param('s', $_COOKIE['userid']);
			$stmt->execute()
			or die("Error. Ping us so we can fix it.");
			$result = $stmt->get_result()
			or die("Error. Ping us so we can fix it.");

			$row = mysqli_fetch_assoc($result);
			$ga = new PHPGangsta_GoogleAuthenticator();

			$qrCodeUrl = $ga->getQRCodeGoogleUrl('TAMU_CTF', $_COOKIE['secret']);
			echo '<img src="'.$qrCodeUrl.'" align="right"/>';
			echo "<b>Name:</b> " . $row['FirstName'] . " " . $row['LastName'];
			echo "<br style='margin-bottom:10px'>";
			echo "<b>Username:</b> " . $row['Username'];
			echo "<br style='margin-bottom:10px'>";
			$phone = $row['Phone'];
			if(!empty($phone)){
				$phone = substr($phone, 0, 3) . "-" . substr($phone, 3, 3) . "-" . substr($phone, 6, 4);
			} else {
				$phone = "*";
			}
			echo "<b>Phone:</b> " . $phone;
			echo "<br style='margin-bottom:10px'>";
			echo "<b>Email:</b> " . $row['Email'];
			echo "<br style='margin-bottom:10px'>";

			$dateTime = explode(" ", $row['CreateDate']);
			$date = $dateTime[0];
			$datePieces = explode("-", $date);
			$date = $datePieces[1] . "/" . $datePieces[2] . "/" . substr($datePieces[0], 2, 2);
			$datetime = $date . " " . substr($dateTime[1], 0, -3);

			echo "<b>Account Created On:</b> " . $datetime;
			echo "<br style='margin-bottom:10px'>";
			echo "<b>Description:</b> " . $row['Description'];
			if($adminCheck) {
				echo "<br style='margin-bottom:10px'>";
				echo "<b>Flag:</b> gigem{th3_T0tp_1s_we4k_w1tH_yoU}";
				echo "<br><br>";
			} else {
				echo "<br><br>";
				echo "<button id='editProfile'>Edit</button>";
			}
			?>
			<script type="text/javascript">
    				document.getElementById("editProfile").onclick = function () {
        				location.href = "/edit";
   	 			};
			</script>
			<br>
	</div>
			<?php if(!$adminCheck) { ?>
	<div id="Messages" class="tabcontent">
		<?php
			$id = $_COOKIE['userid'];
			$stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
			$stmt->bind_param('s', $id);
			$stmt->execute()
			or die("Error. Ping us so we can fix it.");
			$result = $stmt->get_result()
			or die("Error. Ping us so we can fix it.");

			$row = mysqli_fetch_assoc($result);
			$globalUsername = $row['Username'];

			$sql = "SELECT * FROM Messages";
			$result = mysqli_query($conn, $sql)
			or die("Error. Ping us so we can fix it.");
			$num_rows = 0;
			while($row = mysqli_fetch_assoc($result)){
				if($row['MessageTo'] == $globalUsername){
					$num_rows++;
				}
			}
			$page_num = (int)($num_rows / 20)+ 1;
			$result = mysqli_query($conn, $sql)
			or die("Error. Ping us so we can fix it.");

			if(!$adminCheck) {
				echo "<button id=\"sendMessage\">New Message</button>";
			}
		?>

		<script type="text/javascript">
    			document.getElementById("sendMessage").onclick = function () {
        			location.href = "/send";
   	 		};
		</script>
		<div style="border: 1px solid black; margin:5px;">
				<?php
					$id = $_COOKIE['userid'];

					$stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
					$stmt->bind_param('s', $id);
					$stmt->execute()
					or die("Error. Ping us so we can fix it.");
					$result = $stmt->get_result()
					or die("Error. Ping us so we can fix it.");

					$row = mysqli_fetch_assoc($result);
					$globalUsername = $row['Username'];

					$sql = "SELECT * FROM Messages";
					$result = mysqli_query($conn, $sql)
					or die("Error. Ping us so we can fix it.");

					for($i = 1; $i <= $page_num; $i++){
						echo "<div id='messagePage$i' class='messageContent'>";
						echo "<table class='messaging-table'>";
  						echo "<col width='30'>";
  						echo "<col width='280'>";
						echo "<col width='75'>";
						echo "<col width='60'>";
  						echo "<tr>";
    						echo "<th>#</th>";
    						echo "<th>From</th>";
						echo "<th>Date</th>";
						echo "<th>Time</th>";
						echo "<th>Message</th>";
  						echo "</tr>";
						for($j = 1; $j <= 20; $j++){
							if($row = mysqli_fetch_assoc($result)){
								if($row['MessageTo'] == $globalUsername){
									echo "<tr>";
									$messageID = $row['MessageID'];
									echo "<td><form class='messageForm' action='message' method='GET'><input type='hidden' name='id' id='id' value='$messageID'><button class='messageLink'>$j</button></form></td>";
									$firstName = $row['FirstName'];
									$username = $row['Username'];
    									echo "<td>$firstName ($username)</td>";
									$dateTime = explode(" ", $row['CreateDate']);
									$date = $dateTime[0];
									$datePieces = explode("-", $date);
									$date = $datePieces[1] . "/" . $datePieces[2] . "/" . substr($datePieces[0], 2, 2);
									echo "<td>$date</td>";
									$time = substr($dateTime[1], 0, -3);
									echo "<td>$time</td>";
									$message = $row['Message'];
									if(strlen($message) > 40) {
										$message = substr($row['Message'], 0, 40) . "...";
									}
									echo "<td>$message</td>";
									echo "</tr>";
								} else {
									$j--;
								}
							}
						}
						echo "</table>";
						echo "</div>";
					}
				?>
		</div>
		<div class="pagination">
			<?php

				echo "<a class='messagePagelink active' onclick=\"return openMessagePage(event, 'messagePage1')\" href='#' id='defaultMessageOpenPage'>1</a>";
				for($i = 2; $i <= $page_num; $i++){
					echo "<a class='messagePagelink' onclick=\"return openMessagePage(event, 'messagePage$i')\" href='#'>$i</a>";
				}

			?>
		</div>
	</div>
	<div id="Employees" class="tabcontent">
		<?php
			$sql = "SELECT * FROM Users";
			$result = mysqli_query($conn, $sql)
			or die("Error. Ping us so we can fix it.");
			$num_rows = 0;
			while($row = mysqli_fetch_assoc($result)){
				$num_rows++;
			}
			$page_num = (int)($num_rows / 20)+ 1;
			$result = mysqli_query($conn, $sql)
			or die("Error. Ping us so we can fix it.");
		?>
		<div style="border: 1px solid black; margin:5px;">
				<?php
					$sql = "SELECT * FROM Users";
					$result = mysqli_query($conn, $sql)
					or die("Error. Ping us so we can fix it.");

					for($i = 1; $i <= $page_num; $i++){
						echo "<div id='page$i' class='searchContent'>";
						echo "<table class='messaging-table'>";
  						echo "<col width='30'>";
						echo "<col width='175'>";
						echo "<col width='260'>";
						echo "<col width='330'>";
						echo "<col width='130'>";
  						echo "<tr>";
    						echo "<th>ID</th>";
    						echo "<th>Username</th>";
						echo "<th>Name</th>";
						echo "<th>Email</th>";
						echo "<th>Phone</th>";
						echo "<th>Joined</th>";
  						echo "</tr>";
						for($j = 1; $j <= 20; $j++){
							if($row = mysqli_fetch_assoc($result)){
								echo "<tr>";
								$userID = $row['UserID'];
								echo "<td>$userID</td>";
								$username = $row['Username'];
								echo "<td>$username</td>";
								$name = $row['FirstName'] . " " . $row['LastName'];
    								echo "<td>$name</td>";
								$email = $row['Email'];
								echo "<td>$email</td>";
								$phone = $row['Phone'];
								if(!empty($phone)){
									$phone = substr($phone, 0, 3) . "-" . substr($phone, 3, 3) . "-" . substr($phone, 6, 4);
								} else {
									$phone = "*";
								}
								echo "<td>$phone</td>";
								$dateTime = explode(" ", $row['CreateDate']);
								$date = $dateTime[0];
								$datePieces = explode("-", $date);
								$date = $datePieces[1] . "/" . $datePieces[2] . "/" . substr($datePieces[0], 2, 2);
								$datetime = $date . " " . substr($dateTime[1], 0, -3);
								echo "<td>$datetime</td>";
								echo "</tr>";
							}
						}
						echo "</table>";
						echo "</div>";
					}
				?>
		</div>
		<div class="pagination">
			<?php
				echo "<a class='pagelink active' onclick=\"return openPage(event, 'page1')\" href='#' id='defaultOpenPage'>1</a>";
				for($i = 2; $i <= $page_num; $i++){
					echo "<a class='pagelink' onclick=\"return openPage(event, 'page$i')\" href='#'>$i</a>";
				}
			?>
		</div>
	</div>
			<?php } ?>
	<div id="Logout" class="tabcontent">
		<form id='logout' action='logout.php' method='POST' accept-charset='UTF-8'>
			<input type='hidden' name='submitted' id='submitted' value='1' />
			<br>

			<input type='Submit' name='Submit' value='Logout' />
		</form>
	</div>
	<?php
		}
	?>
	<script>
		function openCity(evt, cityName) {
 			var i, tabcontent, tablinks;
			tabcontent = document.getElementsByClassName("tabcontent");
  			for (i = 0; i < tabcontent.length; i++) {
   				tabcontent[i].style.display = "none";
  			}
  			tablinks = document.getElementsByClassName("tablinks");
  			for (i = 0; i < tablinks.length; i++) {
   				tablinks[i].className = tablinks[i].className.replace(" active", "");
 			}
  			document.getElementById(cityName).style.display = "block";
  			evt.currentTarget.className += " active";
  			if(cityName == "Employees"){
   				document.getElementById("defaultOpenPage").click();
   				window.scrollTo(0,0);
  			} else if(cityName == "Messages"){
    				document.getElementById("defaultMessageOpenPage").click();
    				window.scrollTo(0,0);
  			}
		}
		function openPage(event, page) {
			var i, searchcontent, pagelinks;
			searchcontent = document.getElementsByClassName("searchcontent");
			for (i = 0; i < searchcontent.length; i++) {
				searchcontent[i].style.display = "none";
			}
			pagelinks = document.getElementsByClassName("pagelink");
			for (i = 0; i < pagelinks.length; i++) {
				pagelinks[i].className = pagelinks[i].className.replace(" active", "");
			}
			document.getElementById(page).style.display = "block";
			event.currentTarget.className += " active";
		}
		function openMessagePage(event, message) {
			var i, messageContent, messagePagelinks;
			messageContent = document.getElementsByClassName("messageContent");
			for (i = 0; i < messageContent.length; i++) {
				messageContent[i].style.display = "none";
			}
			messagePagelinks = document.getElementsByClassName("messagePagelink");
			for (i = 0; i < messagePagelinks.length; i++) {
				messagePagelinks[i].className = messagePagelinks[i].className.replace(" active", "");
			}
			document.getElementById(message).style.display = "block";
			event.currentTarget.className += " active";
		}
	</script>
	<?php
		if(!isset($_COOKIE['userid']) || !isset($_COOKIE['secret'])) {
	?>
	<script>
		document.getElementById("defaultOpen").click();
	</script>
	<?php
		}
		else{
	?>
	<script>
		document.getElementById("defaultOpen").click();
	</script>
	<?php
		}
	?>
<br><br>
<center><img src="logo.png" align="middle" style="width:600px;height:150px;"></center>
</body>
</html>
