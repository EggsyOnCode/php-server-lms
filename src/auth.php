<?php
function authenticate($email, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, fullname, role FROM user WHERE email = ? AND class = ?");
    $stmt->execute([$email, $password]); // Assuming `class` is used as a password for simplicity.
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
