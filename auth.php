<?php
require_once '../config/database.php';
require_once '../config/session.php';

// Login function
function login($username, $password) {
    $conn = getDBConnection();
    
    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT user_id, username, password_hash, role FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            $stmt->close();
            $conn->close();
            return true;
        }
    }
    
    $stmt->close();
    $conn->close();
    return false;
}

// Logout function
function logout() {
    session_unset();
    session_destroy();
    header('Location: ../pages/login.php');
    exit();
}
?>