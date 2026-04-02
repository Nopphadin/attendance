<?php
session_start();
require 'db.php';

if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

// ดึงข้อมูลนักเรียนทั้งหมด เรียงตามรหัส
$sql = "SELECT * FROM students ORDER BY student_id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เช็คชื่อเข้าเรียน - ระบบลงเวลา</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #f0f2f5; }
        .card-header-custom { background: linear-gradient(135deg, #0d6efd, #0dcaf0); color: white; border-radius: 12px 12px 0 0 !important; }
        .student-row { transition: all 0.2s ease-in-out; }
        .student-row:hover { background-color: #e9ecef; transform: scale(1.01); box-shadow: 0 4px 10px rgba(0,0,0,0.05); z-index: 10; position: relative; }
        .btn-check-label { width: 100px; font-weight: 500; transition: all 0.2s; }
        .btn-check-label:hover { transform: translateY(-2px); }
        .card { border-radius: 12px; }
    </style>
</head>
<body>

<div class="container mt-4 mb-5">
    <div class="card shadow-lg border-0">
        
        <div class="card-header card-header-custom p-4 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">📋 ฟอร์มเช็คชื่อเข้าเรียน (รายวิชา IT)</h3>
            <div>
                <span class="badge bg-light text-dark fs-6 me-2 shadow-sm">ผู้สอน: <?php echo $_SESSION['teacher_name']; ?></span>
                <a href="dashboard.php" class="btn btn-outline-light btn-sm">⬅ กลับหน้าแรก</a>
            </div>
        </div>
        
        <div class="card-body p-0">
            <form action="save_attendance.php" method="POST">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-3">รหัสนักศึกษา</th>
                                <th class="py-3">ชื่อ - นามสกุล</th>
                                <th class="text-center py-3">สถานะการเข้าเรียน</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $sid = $row["student_id"];
                                    echo "<tr class='student-row'>";
                                    
                                    echo "<td class='ps-4'><span class='badge bg-secondary fs-6'>{$sid}</span></td>";
                                    echo "<td><span class='fw-bold text-primary'>{$row['first_name']}</span> <span class='text-muted'>{$row['last_name']}</span></td>";
                                    
                                    // คอลัมน์ปุ่มสถานะ (ตั้งค่า Default ให้มาเรียนทุกคน)
                                    echo "<td class='text-center py-3'>
                                            <div class='btn-group shadow-sm' role='group'>
                                                <input type='radio' class='btn-check' name='status[{$sid}]' id='present_{$sid}' value='Present' checked>
                                                <label class='btn btn-outline-success btn-check-label' for='present_{$sid}'>🟢 มาเรียน</label>

                                                <input type='radio' class='btn-check' name='status[{$sid}]' id='late_{$sid}' value='Late'>
                                                <label class='btn btn-outline-warning btn-check-label' for='late_{$sid}'>🟡 มาสาย</label>

                                                <input type='radio' class='btn-check' name='status[{$sid}]' id='absent_{$sid}' value='Absent'>
                                                <label class='btn btn-outline-danger btn-check-label' for='absent_{$sid}'>🔴 ขาดเรียน</label>
                                            </div>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3' class='text-center py-5 text-danger'>ไม่พบข้อมูลนักเรียนในระบบ</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="p-4 bg-light text-end rounded-bottom">
                    <button type="submit" class="btn btn-primary btn-lg shadow px-5" style="border-radius: 50px;">
                        💾 บันทึกการเช็คชื่อทั้งหมด
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>