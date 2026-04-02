<?php
session_start();
require 'db.php';

// ป้องกันคนไม่ Login แอบเข้า
if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

// ----------------------------------------------------
// ส่วนจัดการรับข้อมูลจาก Modal แล้วบันทึกลงฐานข้อมูล (Backend)
// ----------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_student'])) {
    $student_id = $_POST['student_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $major = "IT"; // สมมติให้ทุกคนอยู่แผนก IT ก่อน

    $sql_insert = "INSERT INTO students (student_id, first_name, last_name, major) 
                   VALUES ('$student_id', '$first_name', '$last_name', '$major')";
    
    if ($conn->query($sql_insert) === TRUE) {
        echo "<script>alert('เพิ่มข้อมูลนักศึกษาเรียบร้อยแล้ว!'); window.location.href='manage_students.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด: รหัสนักศึกษาอาจซ้ำกัน');</script>";
    }
}

// ดึงข้อมูลนักเรียนทั้งหมดมาแสดง
$sql_select = "SELECT * FROM students ORDER BY student_id ASC";
$result = $conn->query($sql_select);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการข้อมูลนักเรียน</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #f8f9fa; }
        .table-custom { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .btn-custom { transition: all 0.3s ease; }
        .btn-custom:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
    </style>
</head>
<body>

<div class="container mt-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>👨‍🎓 จัดการข้อมูลนักเรียน</h2>
        <div>
            <a href="dashboard.php" class="btn btn-secondary btn-custom me-2">⬅ กลับหน้าแรก</a>
            <button type="button" class="btn btn-primary btn-custom" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                ➕ เพิ่มนักเรียนใหม่
            </button>
        </div>
    </div>

    <div class="table-custom p-3">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ลำดับ</th>
                    <th>รหัสนักศึกษา</th>
                    <th>คำนำหน้า - ชื่อ</th>
                    <th>นามสกุล</th>
                    <th>สาขา</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = 1;
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $count++ . "</td>";
                        echo "<td><span class='badge bg-info text-dark'>" . $row['student_id'] . "</span></td>";
                        echo "<td>" . $row['first_name'] . "</td>";
                        echo "<td>" . $row['last_name'] . "</td>";
                        echo "<td>" . $row['major'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>ไม่มีข้อมูลนักเรียน</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addStudentModalLabel">กรอกข้อมูลนักเรียนใหม่</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form method="POST" action="manage_students.php">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">รหัสนักศึกษา (11 หลัก)</label>
                        <input type="text" class="form-control" name="student_id" required placeholder="เช่น 68319010099">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">คำนำหน้า และ ชื่อ</label>
                        <input type="text" class="form-control" name="first_name" required placeholder="เช่น นาย สมชาย">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">นามสกุล</label>
                        <input type="text" class="form-control" name="last_name" required placeholder="เช่น รักเรียน">
                    </div>
                    <input type="hidden" name="add_student" value="1">
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success">💾 บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>