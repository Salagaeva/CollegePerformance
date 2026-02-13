<?php
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

switch ($_SESSION['role']) {
    case 'admin':
        header("Location: admin/admin_panel.php");
        break;
    case 'teacher':
        header("Location: teacher/teacher_panel.php");
        break;
    case 'student':
        header("Location: student/student_panel.php");
        break;
}
exit();
?>
