<?php
  ini_set('display_errors', 'On');
  error_reporting(E_ALL | E_STRICT);
  echo "<html>";
  if (isset($_REQUEST["username"]) && isset($_REQUEST["password"])) {
    $servername = "localhost";
    $username = "sqli-server";
    $password = 'Bx117@$YaML**!';
    $dbname = "SqliDB";
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error)
        die("Connection failed: " . $conn->connect_error);
    // User and pass that were passed to us. WARNING: Password is plaintext!
    $user = $_REQUEST['username'];
    $pass = $_REQUEST['password'];
    $md5pass = md5($pass);
    // Ensure admin will always be the first record, though really unnecessary
    $sql = "SELECT * FROM Users WHERE User='$user' AND Password='$md5pass' ORDER BY ID";
    if ($result = $conn->query($sql)) // Query
    {
      if ($result->num_rows >= 1)
      {
        $row = $result->fetch_assoc();
        echo "You logged in as " . $row["User"];
      }
      else {
        echo "Sorry to say, that's invalid login info!";
      }
    }
    $conn->close();
  }
  else
    echo "Must supply username and password...";
  echo "</html>";
?>
