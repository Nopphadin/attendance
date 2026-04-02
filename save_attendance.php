<?php
session_start();
require 'db.php';

if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ดึงรหัสครูจาก Session ที่ Login อยู่
    $teacher_id = $_SESSION['teacher_id'];
    $attendance_status = $_POST['status'];

    date_default_timezone_set("Asia/Bangkok");
    $check_in_datetime = date("Y-m-d H:i:s");

    foreach ($attendance_status as $student_id => $status) {
        $sql = "INSERT INTO attendance_records (student_id, teacher_id, check_in_datetime, status) 
                VALUES ('$student_id', '$teacher_id', '$check_in_datetime', '$status')";
        
        if ($conn->query($sql) !== TRUE) {
            echo "เกิดข้อผิดพลาดในการบันทึกข้อมูลของ $student_id: " . $conn->error;
            exit();
        }
    }

    echo "<script>
            alert('บันทึกการเช็คชื่อเรียบร้อยแล้ว!');
            window.location.href = 'dashboard.php';
          </script>";
} else {
    echo "ไม่มีการส่งข้อมูลมาครับ";
}
?>