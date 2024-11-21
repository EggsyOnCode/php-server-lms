<?php
session_start();
if (!isset($_SESSION['role'])) {
    header('Location: login.php');
    exit();
}

if ($_SESSION['role'] === 'teacher') {
    header('Location: teacher.php');
} elseif ($_SESSION['role'] === 'student') {
    header('Location: student.php');
} else {
    echo "Unauthorized access.";
}
exit();
?>
