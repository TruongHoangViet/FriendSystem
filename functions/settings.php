<?php
$host = "feenix-mariadb.swin.edu.au";
$user = "s102953779";
$pass = "Silverlord175@yahoo.com";
$db = "s102953779_db";

/*use below for localhost*/
// $host = "localhost";
// $user = "root";
// $pass = "";
// $db = "s102953779_db";

$conn = @mysqli_connect($host, $user, $pass, $db)
or die("<p>Connection failed\n" . "Error Code: "
. mysqli_connect_errno() . ":" . mysqli_connect_error() . "</p>");
?>
