<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าว่าล็อกอินในฐานะอะไร (teacher หรือ student)
    $role = $_POST['role']; 
    // ใช้ real_escape_string เพื่อป้องกันการพิมพ์อักขระแปลกๆ เข้ามาทำลายระบบ (เบื้องต้น)
    $user_id = $conn->real_escape_string($_POST['user_id']);

    if ($role == 'teacher') {
        // --- สำหรับอาจารย์ ---
        $sql = "SELECT * FROM teachers WHERE teacher_id = '$user_id'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['teacher_id'] = $row['teacher_id'];
            $_SESSION['teacher_name'] = $row['first_name'] . " " . $row['last_name'];
            $_SESSION['role'] = 'teacher'; // เก็บสถานะว่านี่คือครู
            header("Location: dashboard.php");
            exit();
        } else {
            $error_teacher = "ไม่พบรหัสผู้สอนนี้ในระบบ!";
        }
    } elseif ($role == 'student') {
        // --- สำหรับนักศึกษา ---
        $sql = "SELECT * FROM students WHERE student_id = '$user_id'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['student_id'] = $row['student_id'];
            $_SESSION['student_name'] = $row['first_name'] . " " . $row['last_name'];
            $_SESSION['role'] = 'student'; // เก็บสถานะว่านี่คือนักศึกษา
            
            // ให้นักศึกษาเด้งไปหน้าสำหรับนักศึกษาโดยเฉพาะ (เดี๋ยวเราจะสร้างไฟล์นี้กัน)
            header("Location: student_dashboard.php");
            exit();
        } else {
            $error_student = "ไม่พบรหัสนักศึกษานี้ในระบบ!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - School Attendance</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Kanit', sans-serif; 
            /* ใส่สีพื้นหลังแบบไล่สี (Gradient) ดูทันสมัย */
            background: linear-gradient(135deg, #74ebd5 0%, #9face6 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
        }
        .login-header {
            background: #fff;
            padding: 30px 20px 15px;
            text-align: center;
        }
        .nav-pills .nav-link {
            border-radius: 50px;
            font-weight: 500;
            color: #6c757d;
        }
        .nav-pills .nav-link.active.teacher-tab { background-color: #0d6efd; color: white; }
        .nav-pills .nav-link.active.student-tab { background-color: #198754; color: white; }
        .btn-teacher { background-color: #0d6efd; border: none; }
        .btn-teacher:hover { background-color: #0b5ed7; }
        .btn-student { background-color: #198754; border: none; }
        .btn-student:hover { background-color: #157347; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 d-flex justify-content-center">
            
            <div class="card login-card border-0">
                <div class="login-header">
                    <h2 class="fw-bold text-dark">🏫 ระบบเช็คชื่อเข้าเรียน</h2>
                    <p class="text-muted">กรุณาเลือกสถานะเพื่อเข้าสู่ระบบ</p>
                </div>
                
                <div class="card-body p-4 pt-0">
                    <ul class="nav nav-pills nav-justified mb-4" id="loginTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active teacher-tab" data-bs-toggle="pill" data-bs-target="#teacher" type="button" role="tab">👨‍🏫 สำหรับผู้สอน</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link student-tab" data-bs-toggle="pill" data-bs-target="#student" type="button" role="tab">👨‍🎓 สำหรับนักศึกษา</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="loginTabsContent">
                        
                        <div class="tab-pane fade show active" id="teacher" role="tabpanel">
                            <?php if(isset($error_teacher)) echo "<div class='alert alert-danger py-2'>$error_teacher</div>"; ?>
                            <form method="POST">
                                <input type="hidden" name="role" value="teacher">
                                <div class="mb-4">
                                    <label class="form-label text-muted">รหัสประจำตัวผู้สอน</label>
                                    <input type="text" name="user_id" class="form-control form-control-lg" placeholder="เช่น T001" required>
                                </div>
                                <button type="submit" class="btn btn-teacher btn-lg w-100 text-white shadow-sm" style="border-radius: 50px;">เข้าสู่ระบบผู้สอน</button>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="student" role="tabpanel">
                            <?php if(isset($error_student)) echo "<div class='alert alert-danger py-2'>$error_student</div>"; ?>
                            <form method="POST">
                                <input type="hidden" name="role" value="student">
                                <div class="mb-4">
                                    <label class="form-label text-muted">รหัสนักศึกษา (11 หลัก)</label>
                                    <input type="text" name="user_id" class="form-control form-control-lg" placeholder="เช่น 68319010039" required>
                                </div>
                                <button type="submit" class="btn btn-student btn-lg w-100 text-white shadow-sm" style="border-radius: 50px;">เข้าสู่ระบบนักศึกษา</button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>