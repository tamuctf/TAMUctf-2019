<?php

function sanitize($searchItem){
	$badInput = false;
	//All the commands that you don't want them to inject.
	$sqlCommands = array (' UNION ', ' CREATE ', ' SHOW ', ' USE ', ' DESCRIBE ', ' DROP ', ' INSERT ', ' DELETE ', ' DROP ', ' INSERT ', ' UPDATE ', ' ALTER ', ' JOIN ');

	for($i = 0; $i < sizeof($sqlCommands); $i++){
		//Looks to see if the command appeared in the string, case-insensitive.
		if(stripos($searchItem, $sqlCommands[$i]) !== false){
			$badInput = true;
			break;
		}
	}
	return $badInput;
}

echo '<html>';
echo '<head>';
echo '<style>';
echo 'img{ display: block; margin-left: auto; margin-right: auto; width: 75%; height: 75%;}';
echo 'h1{ text-align: center; }';
echo '</style>';
echo '</head>';
if(isset($_GET['Search']))
{
	$badInput = false;

	if(strpos($_SERVER['HTTP_USER_AGENT'], 'sqlmap') === false){
		$dbHost = 'localhost';
		$dbUser = 'gigem{w3_4r3_th3_4ggi3s}';
		$dbPass = '1VmHrwxT1iuVag^@PtuDC@KEd421v9';
		$db = 'SqliDB';

		//Creates a connection to the SQL database.
		$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $db)
		or die("Error. Ping us so we can fix it.");

		//Pulls the 'Search' value from the GET paramter.
		$searchItem = trim($_GET['Search']);

		//Check the input for any SQL commands we don't want them to execute.
		$badInput = sanitize($searchItem);

		if(!$badInput){
			//Creates query from user input.
			$sql = "SELECT * FROM Search WHERE items = '$searchItem'";

			//Executes query..
			$result = mysqli_query($conn, $sql)
			or die("<h1> Our search isn't THAT good... </h1> </br> <img src='Ehhh.png'>");

			echo '</br>';
			//If the injection was successful.
			if($result->num_rows > 1){
				echo '<h1>';
				echo 'Nice try, nothing to see here.';
				echo '</h1>';
				echo '</br>';
				echo "<img src='Nice_Going!.gif'>";
			}
			else{
				//Displaying the pre-set values.
				if($row = mysqli_fetch_assoc($result)){
					echo '<h1>';
					echo $row['items'];
					echo '</h1>';
					echo '</br>';
					if(strval($row['items']) == 'Eggs'){
						echo "<img src='Happy_Eggs.png'>";
					}
					elseif(strval($row['items']) == 'Trucks'){
						echo "<img src='Best_Truck.png'>";
					}
					elseif(strval($row['items']) == 'Aggies'){
						echo "<img src='Best_Aggie.png'>";
					}
				}
				else{
					//Injection failure or item not in database.
					echo '<h1>';
					echo "Our search isn't THAT good...";
					echo '</h1>';
					echo '</br>';
					echo "<img src='Ehhh.png'>";
				}
			}
		}
	}
	//Tried to use a SQL command that we don't want them to.
	if($badInput === true){
		echo '</br>';
		echo '<h1>';
		echo "Nope. Not gonna let you use that command.";
		echo '</h1>';
		echo '</br>';
		echo "<img src='Nope.gif'>";
	}
	//Closes the SQL database connection.
	mysql_close($conn);
}
echo '</html>';

?>
