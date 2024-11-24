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
<div class="container large">
    <h2>Welcome, <?php echo $_SESSION['fullname']; ?></h2>
    <h3>Sessions</h3>
    <ul>
        <?php foreach ($sessions as $session): ?>
            <li class="session">
                <p>Class ID: <?php echo $session['id']; ?> </p> 
                <p>Start: <?php echo $session['starttime']; ?> </p> 
                <p>End: <?php echo $session['endtime']; ?> </p>
                <a href="mark_attendance.php?classid=<?php echo $session['id']; ?>">Mark Attendance</a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
