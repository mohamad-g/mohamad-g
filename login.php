<?php
// Include necessary files
require_once '../backend/config/config.php';
require_once '../backend/config/database.php';
require_once '../backend/includes/auth.php';

// Check if form is submitted
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($username) || empty($password)) {
        $error = 'يرجى إدخال اسم المستخدم وكلمة المرور';
    } else {
        // Attempt to authenticate
        $user = authenticate($username, $password);
        
        if ($user) {
            // Create user session
            createUserSession($user);
            
            // Redirect to admin dashboard
            header('Location: index.php');
            exit;
        } else {
            $error = 'اسم المستخدم أو كلمة المرور غير صحيحة';
        }
    }
}

// Check if session expired
if (isset($_GET['expired']) && $_GET['expired'] == 1) {
    $error = 'انتهت صلاحية الجلسة. يرجى تسجيل الدخول مرة أخرى.';
}

// Check if logged out
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    $success = 'تم تسجيل الخروج بنجاح.';
}

// Set language
$lang = $_GET['lang'] ?? DEFAULT_LANGUAGE;
if (!in_array($lang, SUPPORTED_LANGUAGES)) {
    $lang = DEFAULT_LANGUAGE;
}

// Load language strings
$strings = [
    'ar' => [
        'title' => 'تسجيل الدخول',
        'username' => 'اسم المستخدم أو البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'login' => 'تسجيل الدخول',
        'remember_me' => 'تذكرني',
        'forgot_password' => 'نسيت كلمة المرور؟',
        'back_to_site' => 'العودة إلى الموقع',
        'admin_panel' => 'لوحة التحكم'
    ],
    'de' => [
        'title' => 'Anmelden',
        'username' => 'Benutzername oder E-Mail',
        'password' => 'Passwort',
        'login' => 'Anmelden',
        'remember_me' => 'Angemeldet bleiben',
        'forgot_password' => 'Passwort vergessen?',
        'back_to_site' => 'Zurück zur Website',
        'admin_panel' => 'Administrationsbereich'
    ]
];

// Get strings for current language
$s = $strings[$lang];

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $s['title']; ?> - <?php echo $s['admin_panel']; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?php if ($lang === 'ar'): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <?php endif; ?>
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4><?php echo $s['admin_panel']; ?></h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label"><?php echo $s['username']; ?></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label"><?php echo $s['password']; ?></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember"><?php echo $s['remember_me']; ?></label>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary"><?php echo $s['login']; ?></button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <div class="mb-2">
                            <a href="?lang=<?php echo $lang === 'ar' ? 'de' : 'ar'; ?>" class="btn btn-sm btn-outline-secondary">
                                <?php echo $lang === 'ar' ? 'Deutsch' : 'العربية'; ?>
                            </a>
                        </div>
                        <a href="../index.php" class="text-decoration-none">
                            <i class="fas fa-arrow-left"></i> <?php echo $s['back_to_site']; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
