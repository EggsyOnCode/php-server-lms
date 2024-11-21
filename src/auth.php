<?php
function authenticate($email, $password) {
    global $pdo;
    
    // Prepare SQL to select user by email
    $stmt = $pdo->prepare("SELECT id, fullname, role, password FROM user WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if user exists and verify password
    if ($user && password_verify($password, $user['password'])) {
        return $user;  // Return user data if password matches
    }
    
    // Return false if authentication fails
    return false;
}
?>

