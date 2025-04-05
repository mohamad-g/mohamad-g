<?php
// Authentication functions
session_start();

/**
 * Authenticate user with username/email and password
 * 
 * @param string $username Username or email
 * @param string $password Password
 * @return array|bool User data if authenticated, false otherwise
 */
function authenticate($username, $password) {
    global $pdo;
    
    // Check if input is email or username
    $field = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    
    // Prepare query
    $stmt = $pdo->prepare("SELECT * FROM users WHERE $field = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    // Verify password if user exists
    if ($user && password_verify($password, $user['password'])) {
        // Remove password from user data
        unset($user['password']);
        return $user;
    }
    
    return false;
}

/**
 * Create a new session for authenticated user
 * 
 * @param array $user User data
 * @return void
 */
function createUserSession($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['logged_in'] = true;
    $_SESSION['last_activity'] = time();
}

/**
 * Check if user is logged in
 * 
 * @return bool True if logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Check if session is expired
 * 
 * @return bool True if expired, false otherwise
 */
function isSessionExpired() {
    $max_lifetime = SESSION_LIFETIME;
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $max_lifetime)) {
        return true;
    }
    return false;
}

/**
 * Update last activity time
 * 
 * @return void
 */
function updateLastActivity() {
    $_SESSION['last_activity'] = time();
}

/**
 * Logout user
 * 
 * @return void
 */
function logout() {
    // Unset all session variables
    $_SESSION = [];
    
    // Delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
}

/**
 * Require authentication for protected pages
 * 
 * @param string $redirect_url URL to redirect if not authenticated
 * @return void
 */
function requireLogin($redirect_url = 'login.php') {
    if (!isLoggedIn()) {
        header("Location: $redirect_url");
        exit;
    }
    
    if (isSessionExpired()) {
        logout();
        header("Location: $redirect_url?expired=1");
        exit;
    }
    
    updateLastActivity();
}

/**
 * Get current user data
 * 
 * @return array|null User data if logged in, null otherwise
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, username, email, created_at FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Change user password
 * 
 * @param int $user_id User ID
 * @param string $current_password Current password
 * @param string $new_password New password
 * @return bool True if password changed, false otherwise
 */
function changePassword($user_id, $current_password, $new_password) {
    global $pdo;
    
    // Get current user data
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    // Verify current password
    if (!$user || !password_verify($current_password, $user['password'])) {
        return false;
    }
    
    // Hash new password
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => HASH_COST]);
    
    // Update password
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    return $stmt->execute([$hashed_password, $user_id]);
}
