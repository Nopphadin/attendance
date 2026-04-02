<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "school_attendance"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลพัง!: " . $conn->connect_error);
}

// ตั้งค่าให้ดึงข้อมูลภาษาไทยได้ไม่เพี้ยน
$conn->set_charset("utf8");
?>