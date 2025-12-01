<?php
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'uk2_db_pos_loko_campuran';

// Establish connection
$connect = mysqli_connect($hostname, $username, $password, $database);

// Check connection
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}
