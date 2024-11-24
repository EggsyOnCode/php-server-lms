<?php
require_once '../src/config.php'; // Include database configuration

$data = json_decode(file_get_contents('php://input'), true);

$classid = intval($data['classid']);
$date = $data['date'];
$updates = $data['updates'];

try {
    foreach ($updates as $studentId => $attendance) {
        $isPresent = intval($attendance['isPresent']);
        $comments = $attendance['comments'] ?? '';

        // Insert or update attendance
        $query = "INSERT INTO attendance (classid, date, studentid, isPresent, comments)
                  VALUES (:classid, :date, :studentid, :isPresent, :comments)
                  ON DUPLICATE KEY UPDATE isPresent = :isPresent, comments = :comments";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'classid' => $classid,
            'date' => $date,
            'studentid' => $studentId,
            'isPresent' => $isPresent,
            'comments' => $comments
        ]);
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
