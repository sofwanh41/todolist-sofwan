<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "todolist";
// Mencoba melakukan koneksi ke database
$mysqli = new mysqli($servername, $username, $password, $dbname);
// Mengecek koneksi
if ($mysqli->connect_error) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}
