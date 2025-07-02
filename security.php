<?php
// Security configuration for Course Management System

// Prevent direct access to this file
if (!defined('SECURITY_INIT')) {
    die('Direct access not allowed');
}

// Security headers
function setSecurityHeaders() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// Input sanitization
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// SQL injection prevention
function validateInteger($value) {
    return filter_var($value, FILTER_VALIDATE_INT);
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// CSRF protection
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Password hashing
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Rate limiting
function checkRateLimit($identifier, $maxAttempts = 5, $timeWindow = 300) {
    // Simple file-based rate limiting
    $rateLimitFile = sys_get_temp_dir() . '/rate_limit_' . md5($identifier);
    
    if (file_exists($rateLimitFile)) {
        $data = json_decode(file_get_contents($rateLimitFile), true);
        
        if (time() - $data['timestamp'] < $timeWindow) {
            if ($data['attempts'] >= $maxAttempts) {
                return false;
            }
            $data['attempts']++;
        } else {
            $data = ['timestamp' => time(), 'attempts' => 1];
        }
    } else {
        $data = ['timestamp' => time(), 'attempts' => 1];
    }
    
    file_put_contents($rateLimitFile, json_encode($data));
    return true;
}

// Initialize security
setSecurityHeaders();
define('SECURITY_INIT', true);
?>
