<?php
// Session configuration and security
session_start();

// Regenerate session ID periodically
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id();
    $_SESSION['initiated'] = true;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

// Check if user has required role
function hasRole($requiredRole) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $roles = ['Admin', 'Contributor', 'Viewer'];
    $userRoleIndex = array_search($_SESSION['role'], $roles);
    $requiredRoleIndex = array_search($requiredRole, $roles);
    
    return $userRoleIndex !== false && $userRoleIndex <= $requiredRoleIndex;
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../pages/login.php');
        exit();
    }
}

// Redirect if doesn't have required role
function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        header('Location: ../pages/dashboard.php');
        exit();
    }
}
?>