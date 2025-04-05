<?php
// Include necessary files
require_once '../backend/config/config.php';
require_once '../backend/config/database.php';
require_once '../backend/includes/auth.php';
require_once '../backend/includes/image_upload.php';

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
        'image_management' => 'إدارة الصور',
        'profile_cover_images' => 'صور الملف الشخصي والغلاف',
        'article_images' => 'صور المقالات',
        'project_images' => 'صور المشاريع',
        'manage' => 'إدارة',
        'dashboard' => 'لوحة التحكم',
        'profile' => 'الملف الشخصي',
        'articles' => 'المقالات',
        'projects' => 'المشاريع',
        'contact' => 'التواصل',
        'settings' => 'الإعدادات',
        'images' => 'الصور',
        'logout' => 'تسجيل الخروج',
        'change_language' => 'تغيير اللغة',
        'arabic' => 'العربية',
        'german' => 'الألمانية',
        'view_site' => 'عرض الموقع'
    ],
    'de' => [
        'image_management' => 'Bildverwaltung',
        'profile_cover_images' => 'Profil- und Titelbilder',
        'article_images' => 'Artikelbilder',
        'project_images' => 'Projektbilder',
        'manage' => 'Verwalten',
        'dashboard' => 'Dashboard',
        'profile' => 'Profil',
        'articles' => 'Artikel',
        'projects' => 'Projekte',
        'contact' => 'Kontakt',
        'settings' => 'Einstellungen',
        'images' => 'Bilder',
        'logout' => 'Abmelden',
        'change_language' => 'Sprache ändern',
        'arabic' => 'Arabisch',
        'german' => 'Deutsch',
        'view_site' => 'Website anzeigen'
    ]
];

// Get strings for current language
$s = $strings[$lang];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $s['image_management']; ?></title>
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
                            <a class="nav-link" href="settings.php">
                                <i class="fas fa-cog"></i> <?php echo $s['settings']; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="images.php">
                                <i class="fas fa-images"></i> <?php echo $s['images']; ?>
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
                    <h1 class="h2"><?php echo $s['image_management']; ?></h1>
                </div>
                
                <div class="row">
                    <!-- Profile & Cover Images -->
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-user-circle fa-5x mb-3 text-primary"></i>
                                <h5 class="card-title"><?php echo $s['profile_cover_images']; ?></h5>
                                <a href="profile_images.php?lang=<?php echo $lang; ?>" class="btn btn-primary mt-3">
                                    <i class="fas fa-cog"></i> <?php echo $s['manage']; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Article Images -->
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-newspaper fa-5x mb-3 text-success"></i>
                                <h5 class="card-title"><?php echo $s['article_images']; ?></h5>
                                <a href="article_images.php?lang=<?php echo $lang; ?>" class="btn btn-success mt-3">
                                    <i class="fas fa-cog"></i> <?php echo $s['manage']; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Project Images -->
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-project-diagram fa-5x mb-3 text-info"></i>
                                <h5 class="card-title"><?php echo $s['project_images']; ?></h5>
                                <a href="project_images.php?lang=<?php echo $lang; ?>" class="btn btn-info mt-3">
                                    <i class="fas fa-cog"></i> <?php echo $s['manage']; ?>
                                </a>
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
