<?php
session_start();
if ($_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit();
}

require_once '../src/config.php';
require_once '../src/attendance.php';

// Fetch student attendance data
$attendance = getStudentAttendance($_SESSION['user_id']);
?>

<?php
$content = '
<div class="container large">
    <h2>Attendance for ' . $_SESSION['fullname'] . '</h2>
    <ul>
        ' . (empty($attendance) ? '<p>No attendance records found.</p>' : '') . '
        ' . implode('', array_map(function ($record) {
            $statusColor = ($record['class'] === 'red') ? 'red' : (($record['class'] === 'yellow') ? 'yellow' : 'green');
            return '
            <li class="attendance ' . $record['class'] . '">
                <p>Class ID: ' . $record['classid'] . '</p>
                <p>Total Sessions: ' . $record['total_sessions'] . '</p>
                <p>Attended Sessions: ' . $record['attended_sessions'] . '</p>
                <p>Attendance: ' . $record['percentage'] . '%</p>
                <p class="attendance-status" style="color: ' . $statusColor . '">
                   Status: ' . ucfirst($record['class']) . '
                </p>
            </li>';
        }, $attendance)) . '
    </ul>
</div>
';

include '../templates/master.php';
?>
