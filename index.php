<?php
session_start();

if (isset($_SESSION['role'])) {
    header("Location: dashboard.php");
} else {
    header("Location: login.php");
}
exit();
?>