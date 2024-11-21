<?php
session_start();
if ($_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

require_once '../src/config.php';
require_once '../src/attendance.php';

$sessions = getTeacherSessions($_SESSION['user_id']);
?>

<?php include '../templates/master.php'; ?>
<div>
    <h2>Welcome, <?php echo $_SESSION['fullname']; ?></h2>
    <h3>Sessions</h3>
    <ul>
        <?php foreach ($sessions as $session): ?>
            <li>
                Class ID: <?php echo $session['id']; ?> - 
                Start: <?php echo $session['starttime']; ?> - 
                End: <?php echo $session['endtime']; ?> - 
                <a href="mark_attendance.php?classid=<?php echo $session['id']; ?>">Mark Attendance</a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
