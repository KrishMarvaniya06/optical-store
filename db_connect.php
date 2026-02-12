<?php
// db_connect.php
$DB_HOST = 'localhost';
$DB_USER = 'root';      // change if needed
$DB_PASS = '';          // change if needed
$DB_NAME = 'optical_store';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die("DB connect failed: " . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');
