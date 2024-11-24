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


function getStudentsByClassId($classid) {
    global $pdo;
    $query = "SELECT u.id, u.fullname, u.email,
              COALESCE(ROUND((SUM(a.isPresent) / COUNT(a.classid)) * 100, 2), 0) AS attendance_percentage
              FROM user u
              LEFT JOIN attendance a ON u.id = a.studentid AND a.classid = :classid
              WHERE u.class = :classid AND u.role = 'student'
              GROUP BY u.id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['classid' => $classid]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getAttendanceForDate($classid, $date) {
    global $pdo;

    $query = "
        SELECT u.id AS studentid, u.fullname, u.email, 
               a.isPresent, a.comments
        FROM user u
        LEFT JOIN attendance a 
        ON u.id = a.studentid AND a.classid = :classid AND a.date = :date
        WHERE u.role = 'student'
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'classid' => $classid,
        'date' => $date
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getClassDetails($classid) {
    global $pdo;
    $query = "SELECT starttime, endtime FROM class WHERE id = :classid";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['classid' => $classid]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

?>
