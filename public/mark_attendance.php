<?php
session_start();
if ($_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}

require_once '../src/config.php';
require_once '../src/attendance.php';

// Fetch the session ID from the URL
$sessionId = isset($_GET['sessionid']) ? $_GET['sessionid'] : null;

if ($sessionId === null) {
    die("Session ID is missing.");
}

// Fetch the session details to get the class ID
$stmt = $pdo->prepare("SELECT classid FROM sessions WHERE id = ?");
$stmt->execute([$sessionId]);
$session = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$session) {
    die("Session not found.");
}

$classId = $session['classid'];

// Fetch students for the class
$students = getStudentsByClassId($classId);

// Handle form submission to record attendance
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($students as $student) {
        // Default: Present (if checkbox is not set, assume absent)
        $isPresent = isset($_POST['attendance'][$student['id']]) ? 1 : 0;

        // Get status from dropdown (Present = 1, Absent = 0)
        $status = isset($_POST['status'][$student['id']]) && $_POST['status'][$student['id']] === 'Present' ? 1 : 0;

        // Record attendance for the student
        recordAttendance($classId, $sessionId, $student['id'], $isPresent, $status);
    }

    // Redirect back after marking attendance
    header("Location: teacher.php");
    exit();
}
?>

<?php
$content = '
<head>
    <style>
        .container.large {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #4CAF50;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #ddd;
        }

        input[type="checkbox"] {
            width: 20px;
            height: 20px;
        }

        select {
            width: 100%;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 4px;
            margin-top: 20px;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>

<div class="container large">
    <h2>Mark Attendance for Session ' . $sessionId . '</h2>
    <form method="POST">
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Present</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                ' . (empty($students) ? '<tr><td colspan="3">No students found for this session.</td></tr>' : '') . '
                ' . implode('', array_map(function ($student) {
                    return '
                    <tr>
                        <td>' . $student['fullname'] . '</td>
                        <td>
                            <input type="checkbox" name="attendance[' . $student['id'] . ']" value="1" checked />
                        </td>
                        <td>
                            <select name="status[' . $student['id'] . ']">
                                <option value="Present" selected>Present</option>
                                <option value="Absent">Absent</option>
                            </select>
                        </td>
                    </tr>';
                }, $students)) . '
            </tbody>
        </table>
        <button type="submit">Submit Attendance</button>
    </form>
</div>
';

include '../templates/master.php';
?>
