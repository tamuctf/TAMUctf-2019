<?php
  ini_set('display_errors', 'On');
  error_reporting(E_ALL | E_STRICT);

  echo "<html>";
  if (isset($_POST["username"]) && isset($_POST["password"])) {
    $servername = "localhost";
    $username = "sqli-user";
    $password = 'AxU3a9w-azMC7LKzxrVJ^tu5qnM_98Eb';
    $dbname = "SqliDB";


    // Establish connection exists to mysql
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error)
        die("Connection failed: " . $conn->connect_error);

    // User and pass for index.html POST form, in plaintext
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // SQL query syntax checking for matching user/pass in table login
    $sql = "SELECT * FROM login WHERE User='$user' AND Password='$pass'";
    if ($result = $conn->query($sql)) // Actually starting sql query
    {
      if ($result->num_rows >= 1) // If matching rows found...
      {
        $row = $result->fetch_assoc(); 
        if ($row["User"] == "admin") // If admin is matched in query
          echo "gigem{f4rm3r5_f4rm3r5_w3'r3_4ll_r16h7}!";
        else
          echo "You logged in as " . $row["User"];

      }
      else {
        echo "Sorry to say, that's invalid login info!"; // Invalid login
      }
    }

    $conn->close();
  }
  else
    echo "Must supply username and password...";

  echo "</html>";
?>
