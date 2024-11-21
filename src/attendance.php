<?php
function getTeacherSessions($teacherId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM class WHERE teacherid = ?");
    $stmt->execute([$teacherId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getStudentAttendance($studentId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT classid, COUNT(isPresent) AS total_classes, 
        SUM(isPresent) AS attended_classes,
        (SUM(isPresent) / COUNT(isPresent)) * 100 AS percentage
        FROM attendance WHERE studentid = ? GROUP BY classid");
    $stmt->execute([$studentId]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($records as &$record) {
        if ($record['percentage'] < 75) {
            $record['class'] = "red";
        } elseif ($record['percentage'] < 85) {
            $record['class'] = "yellow";
        } else {
            $record['class'] = "green";
        }
    }
    return $records;
}
?>
