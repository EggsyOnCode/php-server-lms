<?php
function getTeacherSessionsForDay($teacherId, $date) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT s.id AS session_id, c.id AS class_id, s.starttime, s.endtime, s.session_date 
        FROM sessions s
        JOIN class c ON s.classid = c.id
        WHERE c.teacherid = ? AND s.session_date = ?");
    $stmt->execute([$teacherId, $date]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getStudentAttendance($studentId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT 
            s.classid, 
            COUNT(a.isPresent) AS total_sessions, 
            SUM(a.isPresent) AS attended_sessions,
            (SUM(a.isPresent) / COUNT(a.isPresent)) * 100 AS percentage
        FROM attendance a
        JOIN sessions s ON a.sessionid = s.id
        WHERE a.studentid = ?
        GROUP BY s.classid");
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

function createSession($classId, $startTime, $endTime, $sessionDate) {
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO sessions (classid, starttime, endtime, session_date) 
        VALUES (?, ?, ?, ?)");
    return $stmt->execute([$classId, $startTime, $endTime, $sessionDate]);
}

function recordAttendance($classId, $sessionId, $studentId, $isPresent, $comments) {
    global $pdo;
    error_log("Recording attendance for class: $classId, session: $sessionId, student: $studentId");

    try {
        // Check if a record already exists
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM attendance 
            WHERE classid = ? AND sessionid = ? AND studentid = ?");
        $stmt->execute([$classId, $sessionId, $studentId]);
        $exists = $stmt->fetchColumn() > 0;

        if ($exists) {
            // Update existing record
            $stmt = $pdo->prepare("
                UPDATE attendance 
                SET isPresent = ?, comments = ? 
                WHERE classid = ? AND sessionid = ? AND studentid = ?");
            $result = $stmt->execute([$isPresent, $comments, $classId, $sessionId, $studentId]);

            if ($result) {
                error_log("Updated attendance for class: $classId, session: $sessionId, student: $studentId");
                return true;
            } else {
                error_log("Failed to update attendance for class: $classId, session: $sessionId, student: $studentId");
                return false;
            }
        } else {
            // Insert new record
            $stmt = $pdo->prepare("
                INSERT INTO attendance (classid, sessionid, studentid, isPresent, comments) 
                VALUES (?, ?, ?, ?, ?)");
            $result = $stmt->execute([$classId, $sessionId, $studentId, $isPresent, $comments]);

            if ($result) {
                error_log("Inserted attendance for class: $classId, session: $sessionId, student: $studentId");
                return true;
            } else {
                error_log("Failed to insert attendance for class: $classId, session: $sessionId, student: $studentId");
                return false;
            }
        }
    } catch (PDOException $e) {
        // Log the exception error message
        error_log("Error: " . $e->getMessage());
        return false;
    }
}




function getStudentsByClassId($classId) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT u.id, u.fullname
        FROM user u
        JOIN enrollments e ON u.id = e.studentid
        WHERE e.classid = ?");
    $stmt->execute([$classId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



?>
