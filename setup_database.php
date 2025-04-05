<?php
// Database setup script
require_once '../config/database.php';

// Create tables
$queries = [
    // Users table
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    
    // Profile table
    "CREATE TABLE IF NOT EXISTS profile (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        language VARCHAR(2) NOT NULL,
        name VARCHAR(100) NOT NULL,
        job_title VARCHAR(100),
        bio TEXT,
        profile_image VARCHAR(255),
        cover_image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY (user_id, language)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    
    // Skills table
    "CREATE TABLE IF NOT EXISTS skills (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        language VARCHAR(2) NOT NULL,
        name VARCHAR(100) NOT NULL,
        `order` INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    
    // Experiences table
    "CREATE TABLE IF NOT EXISTS experiences (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        language VARCHAR(2) NOT NULL,
        title VARCHAR(100) NOT NULL,
        company VARCHAR(100) NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE,
        description TEXT,
        `order` INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    
    // Education table
    "CREATE TABLE IF NOT EXISTS education (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        language VARCHAR(2) NOT NULL,
        degree VARCHAR(100) NOT NULL,
        institution VARCHAR(100) NOT NULL,
        year INT NOT NULL,
        description TEXT,
        `order` INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    
    // Articles table
    "CREATE TABLE IF NOT EXISTS articles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        language VARCHAR(2) NOT NULL,
        title VARCHAR(255) NOT NULL,
        summary TEXT,
        content LONGTEXT,
        image VARCHAR(255),
        published_at TIMESTAMP NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    
    // Projects table
    "CREATE TABLE IF NOT EXISTS projects (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        language VARCHAR(2) NOT NULL,
        title VARCHAR(100) NOT NULL,
        category VARCHAR(100),
        description TEXT,
        image VARCHAR(255),
        url VARCHAR(255),
        `order` INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    
    // Technologies table
    "CREATE TABLE IF NOT EXISTS technologies (
        id INT AUTO_INCREMENT PRIMARY KEY,
        project_id INT NOT NULL,
        language VARCHAR(2) NOT NULL,
        name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    
    // Contact table
    "CREATE TABLE IF NOT EXISTS contact (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        language VARCHAR(2) NOT NULL,
        phone VARCHAR(50),
        email VARCHAR(100),
        address TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY (user_id, language)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    
    // Social Media table
    "CREATE TABLE IF NOT EXISTS social_media (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        platform VARCHAR(50) NOT NULL,
        url VARCHAR(255) NOT NULL,
        icon VARCHAR(50),
        `order` INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
];

// Execute each query
try {
    foreach ($queries as $query) {
        $pdo->exec($query);
    }
    echo "Database tables created successfully.\n";
} catch (PDOException $e) {
    echo "Error creating tables: " . $e->getMessage() . "\n";
}

// Create default admin user if not exists
$username = 'admin';
$email = 'admin@example.com';
$password = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);

try {
    // Check if admin user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // Insert admin user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password]);
        $userId = $pdo->lastInsertId();
        
        // Insert default profile for Arabic
        $stmt = $pdo->prepare("INSERT INTO profile (user_id, language, name, job_title, bio) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, 'ar', 'المدير', 'مدير الموقع', 'مرحباً، أنا مدير هذا الموقع.']);
        
        // Insert default profile for German
        $stmt->execute([$userId, 'de', 'Administrator', 'Website-Administrator', 'Hallo, ich bin der Administrator dieser Website.']);
        
        // Insert default contact for Arabic
        $stmt = $pdo->prepare("INSERT INTO contact (user_id, language, phone, email, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, 'ar', '+123 456 789', $email, 'العنوان هنا']);
        
        // Insert default contact for German
        $stmt->execute([$userId, 'de', '+123 456 789', $email, 'Adresse hier']);
        
        echo "Default admin user created successfully.\n";
    } else {
        echo "Admin user already exists.\n";
    }
} catch (PDOException $e) {
    echo "Error creating admin user: " . $e->getMessage() . "\n";
}
