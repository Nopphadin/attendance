<?php
session_start();
require 'db.php';

// เช็คว่า Login หรือยัง
if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

date_default_timezone_set("Asia/Bangkok");
$today = date("Y-m-d");

// --- ดึงข้อมูลสถิติ ---
$total_students = $conn->query("SELECT COUNT(*) as total FROM students")->fetch_assoc()['total'];

$present = $conn->query("SELECT COUNT(*) as c FROM attendance_records WHERE status = 'Present' AND DATE(check_in_datetime) = '$today'")->fetch_assoc()['c'];
$late = $conn->query("SELECT COUNT(*) as c FROM attendance_records WHERE status = 'Late' AND DATE(check_in_datetime) = '$today'")->fetch_assoc()['c'];
$absent = $conn->query("SELECT COUNT(*) as c FROM attendance_records WHERE status = 'Absent' AND DATE(check_in_datetime) = '$today'")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก - ระบบลงเวลา</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #f4f6f9; }
        .stat-card { transition: transform 0.3s; border-radius: 12px; border: none; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .action-card { text-decoration: none; color: inherit; display: block; }
        .icon-large { font-size: 3rem; margin-bottom: 10px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">🏫 School Attendance</a>
        <div class="d-flex align-items-center">
            <span class="text-light me-3">👤 ครู <?php echo $_SESSION['teacher_name']; ?></span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">ออกจากระบบ</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h3 class="mb-4">📊 สรุปสถิติประจำวัน (<?php echo date("d/m/Y"); ?>)</h3>
    
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card stat-card bg-primary text-white text-center p-4 shadow">
                <h4>นักเรียนทั้งหมด</h4>
                <h1 class="display-4 fw-bold"><?php echo $total_students; ?></h1>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-success text-white text-center p-4 shadow">
                <h4>มาเรียน (🟢)</h4>
                <h1 class="display-4 fw-bold"><?php echo $present; ?></h1>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-warning text-dark text-center p-4 shadow">
                <h4>มาสาย (🟡)</h4>
                <h1 class="display-4 fw-bold"><?php echo $late; ?></h1>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-danger text-white text-center p-4 shadow">
                <h4>ขาดเรียน (🔴)</h4>
                <h1 class="display-4 fw-bold"><?php echo $absent; ?></h1>
            </div>
        </div>
    </div>

    <h3 class="mb-4">🛠️ เมนูการจัดการ</h3>
    
    <div class="row g-4">
        <div class="col-md-4">
            <a href="index.php" class="action-card">
                <div class="card stat-card text-center p-5 shadow-sm h-100 border-primary border-bottom border-4">
                    <div class="icon-large">📝</div>
                    <h4 class="fw-bold text-primary">เช็คชื่อเข้าเรียน</h4>
                    <p class="text-muted">บันทึกเวลาเรียนรายวัน</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="manage_students.php" class="action-card">
                <div class="card stat-card text-center p-5 shadow-sm h-100 border-success border-bottom border-4">
                    <div class="icon-large">👨‍🎓</div>
                    <h4 class="fw-bold text-success">จัดการข้อมูลนักเรียน</h4>
                    <p class="text-muted">เพิ่ม ลบ หรือแก้ไขข้อมูลนักศึกษา</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="report.php" class="action-card">
                <div class="card stat-card text-center p-5 shadow-sm h-100 border-info border-bottom border-4">
                    <div class="icon-large">📅</div>
                    <h4 class="fw-bold text-info">ประวัติการเข้าเรียน</h4>
                    <p class="text-muted">ดูสรุปและประวัติการเข้าเรียนย้อนหลัง</p>
                </div>
            </a>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>