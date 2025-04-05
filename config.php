<?php
// Application configuration
define('BASE_URL', 'http://localhost/website_project');
define('ADMIN_URL', BASE_URL . '/admin');
define('API_URL', BASE_URL . '/backend/api');

// Upload directories
define('UPLOAD_DIR', $_SERVER['DOCUMENT_ROOT'] . '/website_project/uploads');
define('PROFILE_UPLOAD_DIR', UPLOAD_DIR . '/profile');
define('COVER_UPLOAD_DIR', UPLOAD_DIR . '/cover');
define('ARTICLES_UPLOAD_DIR', UPLOAD_DIR . '/articles');
define('PROJECTS_UPLOAD_DIR', UPLOAD_DIR . '/projects');

// Upload URLs
define('UPLOAD_URL', BASE_URL . '/uploads');
define('PROFILE_UPLOAD_URL', UPLOAD_URL . '/profile');
define('COVER_UPLOAD_URL', UPLOAD_URL . '/cover');
define('ARTICLES_UPLOAD_URL', UPLOAD_URL . '/articles');
define('PROJECTS_UPLOAD_URL', UPLOAD_URL . '/projects');

// Session configuration
define('SESSION_NAME', 'personal_website_session');
define('SESSION_LIFETIME', 86400); // 24 hours

// Security
define('HASH_COST', 12); // For password hashing

// Languages
define('DEFAULT_LANGUAGE', 'ar');
define('SUPPORTED_LANGUAGES', ['ar', 'de']);
