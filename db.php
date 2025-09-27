<?php
$host = "sql213.infinityfree.com";
$username = "if0_40035410";
$password = "OSTNuo5cBq1";
$database = "if0_40035410_db_test";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
