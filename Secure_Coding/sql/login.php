<?php
  ini_set('display_errors', 'On');
  error_reporting(E_ALL | E_STRICT);
  echo "<html>";
  if (isset($_POST["username"]) && isset($_POST["password"])) {
    $servername = "localhost";
    $username = "sqli-user";
    $password = 'AxU3a9w-azMC7LKzxrVJ^tu5qnM_98Eb';
    $dbname = "SqliDB";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error)
        die("Connection failed: " . $conn->connect_error);
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $sql = "SELECT * FROM login WHERE User='$user' AND Password='$pass'";
    if ($result = $conn->query($sql))
    {
      if ($result->num_rows >= 1)
      {
        $row = $result->fetch_assoc(); 
        echo "You logged in as " . $row["User"];
        $row = $result->fetch_assoc();
        echo "<html>You logged in as " . $row["User"] . "</html>\n";
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
