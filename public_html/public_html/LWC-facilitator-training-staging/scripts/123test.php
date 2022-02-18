<?php
$servername = "localhost";
$username = "ealtibj2_rural1";
$password = "Rur4l!!";
$dbname = "ealtibj2_rural1";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$sql = "SELECT * FROM users_roles WHERE rid = 5";
$result = $conn->query($sql);

	//if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
    	$orgusers = $row["uid"];
        echo "id: " . $row["uid"]. " - Name: " . $row["rid"]. "<br> ";
        
        	$sql2 = "SELECT * FROM users_roles WHERE uid = $orgusers AND rid <> 5 AND rid <> 6 LIMIT 1";
        	//select each of the orgs roles except 5 and 6 which are all the trainings and orgs shared roles
        	
			$result2 = $conn->query($sql2);
			while($row2 = $result2->fetch_assoc()) {
			$orgusers2 = $row2["uid"];
			$orgroles2 = $row2["rid"];
			
			echo "-----org: $orgusers2 | roles $orgroles2 <br>";
			
					$sql3 = "SELECT * FROM users_roles WHERE rid = $orgroles2 AND uid <> $orgusers2";
					$result3 = $conn->query($sql3);
					while($row3 = $result3->fetch_assoc()) {
					$orgusers3 = $row3["uid"];
					$orgroles3 = $row3["rid"];
					
					echo "---------------- Orgs Users: $orgusers3 | users' roles $orgroles3<br>";
					
						$sql4 = "SELECT * FROM users_roles WHERE uid = $orgusers3 AND rid <> $orgroles3 AND rid <> 6";
						$result4 = $conn->query($sql4);
						while($row4 = $result4->fetch_assoc()) {
						$orgusers4 = $row4["uid"];
						$orgroles4 = $row4["rid"];	
							
						echo "--------------------- Orgs User: $orgusers4 | users' additional roles $orgroles4<br>";	
							
						} // fourth while
					
					
					} // third while
			
			
			} //second while
        
        echo "<br><br>";
        
    } //first while


$conn->close();
?>