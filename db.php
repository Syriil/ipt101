<?php
//Database connection parameters
$sname = "localhost:"; // Server name and port
$uname = "root"; // Database username
$password = ""; // Database password
$db_name = "ipt101"; // Database name


$conn = mysqli_connect($sname, $uname, $password, $db_name);


if (!$conn) {
    echo "Failed to connect to the database"; 
} else {
    
}
?>          