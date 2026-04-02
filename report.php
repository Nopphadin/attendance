<?php
session_start();
require 'db.php';

// ป้องกันคนไม่ Login แอบเข้า
if (!isset($_SESSION['teacher_id'])) {
    header("Location: login.php");
    exit();
}

// --- ระบบค้นหา (Search & Filter) ---
$search_query = "";
$where_clause = "1=1"; // ค่าเริ่มต้น ดึงข้อมูลทั้งหมด

// ถ้ามีการกดปุ่มค้นหา
if (isset($_GET['search'])) {
    $keyword = $conn->real_escape_string($_GET['keyword']);
    $search_date = $conn->real_escape_string($_GET['search_date']);

    // ถ้าพิมพ์คำค้นหา (ชื่อ หรือ รหัส)
    if (!empty($keyword)) {
        $where_clause .= " AND (s.student_id LIKE '%$keyword%' OR s.first_name LIKE '%$keyword%' OR s.last_name LIKE '%$keyword%')";
    }
    // ถ้าเลือกวันที่
    if (!empty($search_date)) {
        $where_clause .= " AND DATE(a.check_in_datetime) = '$search_date'";
    }
}

// คำสั่ง SQL ที่รวมระบบค้นหาแล้ว
$sql = "SELECT a.check_in_datetime, s.student_id, s.first_name, s.last_name, a.status 
        FROM attendance_records a 
        JOIN students s ON a.student_id = s.student_id 
        WHERE $where_clause
        ORDER BY a.check_in_datetime DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ประวัติการเข้าเรียน - School Attendance</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Kanit', sans-serif; background-color: #f4f6f9; }
        .card-header-custom { background: linear-gradient(135deg, #6f42c1, #0dcaf0); color: white; border-radius: 12px 12px 0 0 !important; }
        .table-custom { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="container mt-5 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📅 ประวัติการเข้าเรียนทั้งหมด</h2>
        <a href="dashboard.php" class="btn btn-secondary">⬅ กลับหน้าแรก</a>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="report.php" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label text-muted">🔍 ค้นหารหัส หรือ ชื่อนักศึกษา</label>
                    <input type="text" name="keyword" class="form-control" placeholder="พิมพ์ชื่อที่ต้องการหา..." value="<?php echo isset($_GET['keyword']) ? $_GET['keyword'] : ''; ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted">📆 เลือกวันที่</label>
                    <input type="date" name="search_date" class="form-control" value="<?php echo isset($_GET['search_date']) ? $_GET['search_date'] : ''; ?>">
                </div>
                <div class="col-md-3">
                    <button type="submit" name="search" class="btn btn-primary w-100">ค้นหาข้อมูล</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-lg border-0">
        <div class="card-header card-header-custom p-3">
            <h5 class="mb-0">📋 รายงานผลการเช็คชื่อ</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3">วัน-เวลาที่เช็คชื่อ</th>
                            <th class="py-3">รหัสนักศึกษา</th>
                            <th class="py-3">ชื่อ - นามสกุล</th>
                            <th class="py-3 text-center">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td class='ps-4 text-muted'>" . date("d/m/Y H:i", strtotime($row['check_in_datetime'])) . "</td>";
                                echo "<td><span class='badge bg-secondary fs-6'>" . $row['student_id'] . "</span></td>";
                                echo "<td class='fw-bold text-dark'>" . $row['first_name'] . " " . $row['last_name'] . "</td>";
                                
                                // ปรับสีป้ายสถานะ (Badge) ให้สวยงาม
                                echo "<td class='text-center'>";
                                if($row['status'] == 'Present') {
                                    echo "<span class='badge bg-success px-3 py-2'>🟢 มาเรียน</span>";
                                } elseif($row['status'] == 'Late') {
                                    echo "<span class='badge bg-warning text-dark px-3 py-2'>🟡 มาสาย</span>";
                                } else {
                                    echo "<span class='badge bg-danger px-3 py-2'>🔴 ขาดเรียน</span>";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center py-5 text-danger'>ไม่พบข้อมูลการเช็คชื่อที่ค้นหา</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>