<?php
session_start();
if ($_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit();
}

require_once '../src/config.php';
require_once '../src/attendance.php';

$attendance = getStudentAttendance($_SESSION['user_id']);
?>

<?php include '../templates/master.php'; ?>
<div class="container large">
    <h2>Attendance for <?php echo $_SESSION['fullname']; ?></h2>
    <ul>
        <?php foreach ($attendance as $record): ?>
            <li class="<?php echo $record['class']; ?> attendance">
                <p> Class ID: <?php echo $record['classid']; ?> </p>
                <p> Attendance: <?php echo $record['percentage']; ?>% </p>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
