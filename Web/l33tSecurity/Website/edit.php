<?php
	include 'sql_connect.php';
	global $conn;
	include 'cookie_check.php';
	global $cookieCheck;
	include 'admin_check.php';
	global $adminCheck;

	if(!$cookieCheck){
		echo "ERROR: Need to sign in to a valid account to edit a profile.";
		header( "refresh:5;url=/" );
	} else if ($adminCheck) {
		header( "Location: /" );
	} else {
		$stmt = $conn->prepare("SELECT FirstName, LastName, Phone, Description FROM Users WHERE UserID = ?");
		$stmt->bind_param("s", $_COOKIE['userid']);
		$stmt->execute()
		or die("Error. Ping us so we can fix it.");
		$result = $stmt->get_result()
		or die("Error. Ping us so we can fix it.");

		$row = mysqli_fetch_assoc($result);
		$firstname; $lastname; $phone; $description;
		if(!empty($row['FirstName'])){
			$firstname = $row['FirstName'];
		} else {
			$firstname = "";
		}
		if(!empty($row['LastName'])){
			$lastname = $row['LastName'];
		} else {
			$lastname = "";
		}
		if(!empty($row['Phone'])){
			$phone = $row['Phone'];
		} else {
			$phone = "";
		}
		if(!empty($row['Description'])){
			$description = $row['Description'];
		} else {
			$description = "";
		}
?>

<form id='register-user' action='apply_edit' method='POST' accept-charset='UTF-8'>
	<fieldset style="width:25%">
		<legend>Edit Profile</legend>
		<div style="float:left">
			<label for='firstname'>First Name:</label>
			<br>
			<input type='text' name='firstname' id='firstname' maxlength="10" pattern = "[a-zA-Z]+" value=<?php echo "\"$firstname\"";?>/>
		</div>
		<div>
			<label for='lastname'>Last Name:</label>
			<br>
			<input type='text' name='lastname' id='lastname' maxlength="15" pattern = "[a-zA-Z]+" value=<?php echo "\"$lastname\"";?>/>
		</div>
		<div style="clear: both;"></div>
		<br>

		<div style="float:left">
			<label for='phone'>Phone Number:</label>
			<br>
			<input type="tel" id="phone" name="phone" pattern="[0-9]{10}" oninvalid="this.setCustomValidity('Please Enter Correct Phone # - ex. 1234567891')" oninput="this.setCustomValidity('')" value=<?php echo "\"$phone\"";?>>
		</div>
		<div>
			<label for='password'>Password:</label>
			<br>
			<input type='password' name='password' id='password' maxlength="50"/>
		</div>
		<div style="clear: both;"></div>
		<br>

		<label for='description'>Description:</label>
		<br>
		<textarea maxlength='10000' name='description' id='description' style='margin-top: 5px; resize: none' rows='4' cols='53'><?php echo $description;?></textarea>
		<br><br>
		<input type='Submit' name='Submit' value='Apply' />
		<pre style="display:inline"> </pre>
	</fieldset>
</form>
<pre style="display:inline"> </pre>
<button id='back'>Back</button>
<script type="text/javascript">
    	document.getElementById("back").onclick = function () {
        	location.href = "/";
   	};
</script>
<?php
	}
?>
