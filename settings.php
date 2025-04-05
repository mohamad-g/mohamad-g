<?php
// Include necessary files
require_once '../backend/config/config.php';
require_once '../backend/config/database.php';
require_once '../backend/includes/auth.php';

// Require login
requireLogin('login.php');

// Get current user
$user = getCurrentUser();

// Set language
$lang = $_GET['lang'] ?? DEFAULT_LANGUAGE;
if (!in_array($lang, SUPPORTED_LANGUAGES)) {
    $lang = DEFAULT_LANGUAGE;
}

// Load language strings
$strings = [
    'ar' => [
        'settings' => 'الإعدادات',
        'account_settings' => 'إعدادات الحساب',
        'change_password' => 'تغيير كلمة المرور',
        'current_password' => 'كلمة المرور الحالية',
        'new_password' => 'كلمة المرور الجديدة',
        'confirm_password' => 'تأكيد كلمة المرور',
        'save_changes' => 'حفظ التغييرات',
        'account_info' => 'معلومات الحساب',
        'username' => 'اسم المستخدم',
        'email' => 'البريد الإلكتروني',
        'created_at' => 'تاريخ الإنشاء',
        'dashboard' => 'لوحة التحكم',
        'profile' => 'الملف الشخصي',
        'articles' => 'المقالات',
        'projects' => 'المشاريع',
        'contact' => 'التواصل',
        'logout' => 'تسجيل الخروج',
        'change_language' => 'تغيير اللغة',
        'arabic' => 'العربية',
        'german' => 'الألمانية',
        'view_site' => 'عرض الموقع',
        'password_updated' => 'تم تحديث كلمة المرور بنجاح',
        'password_error' => 'خطأ في تحديث كلمة المرور',
        'password_mismatch' => 'كلمة المرور الجديدة وتأكيدها غير متطابقين',
        'current_password_wrong' => 'كلمة المرور الحالية غير صحيحة'
    ],
    'de' => [
        'settings' => 'Einstellungen',
        'account_settings' => 'Kontoeinstellungen',
        'change_password' => 'Passwort ändern',
        'current_password' => 'Aktuelles Passwort',
        'new_password' => 'Neues Passwort',
        'confirm_password' => 'Passwort bestätigen',
        'save_changes' => 'Änderungen speichern',
        'account_info' => 'Kontoinformationen',
        'username' => 'Benutzername',
        'email' => 'E-Mail',
        'created_at' => 'Erstellt am',
        'dashboard' => 'Dashboard',
        'profile' => 'Profil',
        'articles' => 'Artikel',
        'projects' => 'Projekte',
        'contact' => 'Kontakt',
        'logout' => 'Abmelden',
        'change_language' => 'Sprache ändern',
        'arabic' => 'Arabisch',
        'german' => 'Deutsch',
        'view_site' => 'Website anzeigen',
        'password_updated' => 'Passwort erfolgreich aktualisiert',
        'password_error' => 'Fehler beim Aktualisieren des Passworts',
        'password_mismatch' => 'Neues Passwort und Bestätigung stimmen nicht überein',
        'current_password_wrong' => 'Aktuelles Passwort ist falsch'
    ]
];

// Get strings for current language
$s = $strings[$lang];

// Process form submission
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validate input
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $message = 'جميع الحقول مطلوبة';
        $messageType = 'danger';
    } elseif ($newPassword !== $confirmPassword) {
        $message = $s['password_mismatch'];
        $messageType = 'danger';
    } else {
        // Attempt to change password
        $result = changePassword($user['id'], $currentPassword, $newPassword);
        
        if ($result) {
            $message = $s['password_updated'];
            $messageType = 'success';
        } else {
            $message = $s['current_password_wrong'];
            $messageType = 'danger';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $s['settings']; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?php if ($lang === 'ar'): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <?php endif; ?>
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h5 class="text-white"><?php echo $s['dashboard']; ?></h5>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-tachometer-alt"></i> <?php echo $s['dashboard']; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">
                                <i class="fas fa-user"></i> <?php echo $s['profile']; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="articles.php">
                                <i class="fas fa-newspaper"></i> <?php echo $s['articles']; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="projects.php">
                                <i class="fas fa-project-diagram"></i> <?php echo $s['projects']; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact.php">
                                <i class="fas fa-address-card"></i> <?php echo $s['contact']; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="settings.php">
                                <i class="fas fa-cog"></i> <?php echo $s['settings']; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php?logout=1">
                                <i class="fas fa-sign-out-alt"></i> <?php echo $s['logout']; ?>
                            </a>
                        </li>
                    </ul>
                    
                    <hr class="text-white-50">
                    
                    <div class="dropdown px-3 mb-3">
                        <button class="btn btn-secondary dropdown-toggle w-100" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo $s['change_language']; ?>
                        </button>
                        <ul class="dropdown-menu w-100" aria-labelledby="languageDropdown">
                            <li><a class="dropdown-item <?php echo $lang === 'ar' ? 'active' : ''; ?>" href="?lang=ar"><?php echo $s['arabic']; ?></a></li>
                            <li><a class="dropdown-item <?php echo $lang === 'de' ? 'active' : ''; ?>" href="?lang=de"><?php echo $s['german']; ?></a></li>
                        </ul>
                    </div>
                    
                    <div class="px-3 mb-3">
                        <a href="../index.php" class="btn btn-outline-light w-100">
                            <i class="fas fa-external-link-alt"></i> <?php echo $s['view_site']; ?>
                        </a>
                    </div>
                </div>
            </nav>
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?php echo $s['settings']; ?></h1>
                </div>
                
                <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <!-- Account Information -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><?php echo $s['account_info']; ?></h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $s['username']; ?></label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $s['email']; ?></label>
                                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo $s['created_at']; ?></label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['created_at']); ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Change Password -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5><?php echo $s['change_password']; ?></h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label"><?php echo $s['current_password']; ?></label>
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label"><?php echo $s['new_password']; ?></label>
                                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label"><?php echo $s['confirm_password']; ?></label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    </div>
                                    <button type="submit" name="change_password" class="btn btn-primary"><?php echo $s['save_changes']; ?></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
