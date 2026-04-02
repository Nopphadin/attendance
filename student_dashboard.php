<?php
session_start();
require 'db.php';

// เช็คว่าใช่ 'นักศึกษา' ที่ Login เข้ามาหรือเปล่า
if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// ดึงข้อมูลประวัติการเช็คชื่อเฉพาะของนักศึกษาคนนี้
$sql = "SELECT * FROM attendance_records WHERE student_id = '$student_id' ORDER BY check_in_datetime DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าของนักศึกษา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>body { font-family: 'Kanit', sans-serif; background-color: #f8f9fa; }</style>
</head>
<body>

<nav class="navbar navbar-dark bg-success shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">👨‍🎓 Student Portal</a>
        <div>
            <span class="text-white me-3">สวัสดี, <?php echo $_SESSION['student_name']; ?></span>
            <a href="logout.php" class="btn btn-light btn-sm text-success">ออกจากระบบ</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h4 class="mb-4">📅 ประวัติการเข้าเรียนของคุณ</h4>
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>วันที่และเวลา</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            // ปรับสีและคำตามสถานะ
                            if($row['status'] == 'Present') $badge = "<span class='badge bg-success'>มาเรียน</span>";
                            elseif($row['status'] == 'Late') $badge = "<span class='badge bg-warning text-dark'>มาสาย</span>";
                            else $badge = "<span class='badge bg-danger'>ขาดเรียน</span>";

                            echo "<tr>";
                            echo "<td>" . date("d/m/Y H:i", strtotime($row['check_in_datetime'])) . "</td>";
                            echo "<td>" . $badge . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2' class='text-center text-muted'>ยังไม่มีประวัติการเข้าเรียน</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>a