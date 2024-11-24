<?php
session_start();
if ($_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

require_once '../src/config.php';
require_once '../src/attendance.php';

// Assuming you want to show sessions for today's date
$date = date('Y-m-d'); // Get today's date
$sessions = getTeacherSessionsForDay($_SESSION['user_id'], $date);
?>

<?php
$content = '
<div class="container large">
    <h2>Welcome, ' . $_SESSION['fullname'] . '</h2>
    <h3>Sessions for ' . $date . '</h3>
    ' . (empty($sessions) ? '<p>No sessions scheduled for today.</p>' : '
    <ul>
        ' . implode('', array_map(function ($session) {
            return '
            <li class="session">
                <p>Class ID: ' . $session['class_id'] . '</p>
                <p>Start: ' . $session['starttime'] . '</p>
                <p>End: ' . $session['endtime'] . '</p>
                <a href="mark_attendance.php?sessionid=' . $session['session_id'] . '">Mark Attendance</a>
            </li>';
        }, $sessions)) . '
    </ul>') . '
    <div class="create-session">
        <a href="create_session.php" class="btn">Create New Session</a>
    </div>
</div>
';
include '../templates/master.php';
?>

