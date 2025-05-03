<?php

$hostname = "mysql";
$username = "imp";
$password = "imP.kS";
$dbname = "KSZadanie";

define("HOSTNAME", "mysql");
define("USERNAME", "imp");
define("PASSWORD", "imP.kS");
define("DBNAME", "KSZadanie");

$dbconfig = array(
    'hostname' => 'mysql',
    'username' => 'imp',
    'password' => 'imP.kS',
    'dbname' => 'KSZadanie',
);

$conn = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);

if (! $conn) {
    die("Connection failed: ".$conn->errorInfo());
}