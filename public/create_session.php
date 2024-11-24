<?php
session_start();
if ($_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

require_once '../src/config.php';
include '../src/attendance.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classId = $_POST['classid'];
    $startTime = $_POST['starttime'];
    $endTime = $_POST['endtime'];
    $sessionDate = $_POST['session_date'];

    // Call the createSession function
    if (createSession($classId, $startTime, $endTime, $sessionDate)) {
        $message = "Session created successfully!";
    } else {
        $message = "Error creating session.";
        head('Location: teacher.php');
    }

}

$content = '

<div class="container large">
    <h2>Create a New Session</h2>
    ' . (isset($message) ? "<p>$message</p>" : '') . '
    <form method="POST">
        <div class="form-group">
            <label for="classid">Class ID:</label>
            <input type="text" id="classid" name="classid" required>
        </div>
        <div class="form-group">
            <label for="starttime">Start Time:</label>
            <input type="time" id="starttime" name="starttime" required>
        </div>
        <div class="form-group">
            <label for="endtime">End Time:</label>
            <input type="time" id="endtime" name="endtime" required>
        </div>
        <div class="form-group">
            <label for="session_date">Session Date:</label>
            <input type="date" id="session_date" name="session_date" required>
        </div>
        <button type="submit">Create Session</button>
    </form>
</div>
';

include '../templates/master.php';  
?>
