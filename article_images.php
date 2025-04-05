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
        'article_images' => 'صور المقالات',
        'article_image_upload' => 'رفع صور المقالات',
        'select_article' => 'اختر المقال',
        'current_images' => 'الصور الحالية',
        'upload_new_image' => 'رفع صورة جديدة',
        'select_image' => 'اختر صورة',
        'upload' => 'رفع',
        'delete' => 'حذف',
        'no_images' => 'لا توجد صور',
        'no_articles' => 'لا توجد مقالات',
        'image_uploaded' => 'تم رفع الصورة بنجاح',
        'image_deleted' => 'تم حذف الصورة بنجاح',
        'error_occurred' => 'حدث خطأ. يرجى المحاولة مرة أخرى.',
        'allowed_types' => 'الأنواع المسموح بها: JPG, PNG, GIF',
        'max_size' => 'الحجم الأقصى: 5 ميجابايت',
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
        'view_site' => 'عرض الموقع',
        'save_changes' => 'حفظ التغييرات',
        'cancel' => 'إلغاء',
        'back' => 'رجوع'
    ],
    'de' => [
        'article_images' => 'Artikelbilder',
        'article_image_upload' => 'Artikelbilder hochladen',
        'select_article' => 'Artikel auswählen',
        'current_images' => 'Aktuelle Bilder',
        'upload_new_image' => 'Neues Bild hochladen',
        'select_image' => 'Bild auswählen',
        'upload' => 'Hochladen',
        'delete' => 'Löschen',
        'no_images' => 'Keine Bilder vorhanden',
        'no_articles' => 'Keine Artikel vorhanden',
        'image_uploaded' => 'Bild erfolgreich hochgeladen',
        'image_deleted' => 'Bild erfolgreich gelöscht',
        'error_occurred' => 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.',
        'allowed_types' => 'Erlaubte Dateitypen: JPG, PNG, GIF',
        'max_size' => 'Maximale Größe: 5 MB',
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
        'view_site' => 'Website anzeigen',
        'save_changes' => 'Änderungen speichern',
        'cancel' => 'Abbrechen',
        'back' => 'Zurück'
    ]
];

// Get strings for current language
$s = $strings[$lang];

// Process form submission
$message = '';
$messageType = '';

// Get articles
try {
    $stmt = $pdo->prepare("SELECT id, title FROM articles WHERE user_id = ? AND language = ? ORDER BY title ASC");
    $stmt->execute([$user['id'], $lang]);
    $articles = $stmt->fetchAll();
} catch (PDOException $e) {
    $message = $s['error_occurred'];
    $messageType = 'danger';
    $articles = [];
}

// Get article ID
$articleId = isset($_GET['article_id']) ? (int)$_GET['article_id'] : (isset($_POST['article_id']) ? (int)$_POST['article_id'] : 0);

// Get article images
$articleImages = [];
if ($articleId) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM article_images WHERE article_id = ? ORDER BY id DESC");
        $stmt->execute([$articleId]);
        $articleImages = $stmt->fetchAll();
    } catch (PDOException $e) {
        $message = $s['error_occurred'];
        $messageType = 'danger';
    }
}

// Upload article image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_article_image']) && isset($_FILES['article_image']) && $articleId) {
    $uploadDir = UPLOADS_DIR . '/articles';
    $result = uploadImage($_FILES['article_image'], $uploadDir);
    
    if ($result['success']) {
        try {
            // Insert article image
            $stmt = $pdo->prepare("INSERT INTO article_images (article_id, filename) VALUES (?, ?)");
            $stmt->execute([$articleId, $result['fileName']]);
            
            $message = $s['image_uploaded'];
            $messageType = 'success';
            
            // Refresh article images
            $stmt = $pdo->prepare("SELECT * FROM article_images WHERE article_id = ? ORDER BY id DESC");
            $stmt->execute([$articleId]);
            $articleImages = $stmt->fetchAll();
        } catch (PDOException $e) {
            $message = $s['error_occurred'];
            $messageType = 'danger';
        }
    } else {
        $message = $result['message'];
        $messageType = 'danger';
    }
}

// Delete article image
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_article_image']) && isset($_POST['image_id'])) {
    $imageId = (int)$_POST['image_id'];
    
    try {
        // Get image filename
        $stmt = $pdo->prepare("SELECT filename FROM article_images WHERE id = ?");
        $stmt->execute([$imageId]);
        $image = $stmt->fetch();
        
        if ($image) {
            $uploadDir = UPLOADS_DIR . '/articles';
            $result = deleteImage($image['filename'], $uploadDir);
            
            if ($result['success']) {
                // Delete image from database
                $stmt = $pdo->prepare("DELETE FROM article_images WHERE id = ?");
                $stmt->execute([$imageId]);
                
                $message = $s['image_deleted'];
                $messageType = 'success';
                
                // Refresh article images
                $stmt = $pdo->prepare("SELECT * FROM article_images WHERE article_id = ? ORDER BY id DESC");
                $stmt->execute([$articleId]);
                $articleImages = $stmt->fetchAll();
            } else {
                $message = $result['message'];
                $messageType = 'danger';
            }
        }
    } catch (PDOException $e) {
        $message = $s['error_occurred'];
        $messageType = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $s['article_images']; ?></title>
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
                            <li><a class="dropdown-item <?php echo $lang === 'ar' ? 'active' : ''; ?>" href="?lang=ar<?php echo $articleId ? '&article_id=' . $articleId : ''; ?>"><?php echo $s['arabic']; ?></a></li>
                            <li><a class="dropdown-item <?php echo $lang === 'de' ? 'active' : ''; ?>" href="?lang=de<?php echo $articleId ? '&article_id=' . $articleId : ''; ?>"><?php echo $s['german']; ?></a></li>
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
                    <h1 class="h2"><?php echo $s['article_images']; ?></h1>
                    <a href="images.php?lang=<?php echo $lang; ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-<?php echo $lang === 'ar' ? 'right' : 'left'; ?>"></i> <?php echo $s['back']; ?>
                    </a>
                </div>
                
                <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Select Article -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><?php echo $s['select_article']; ?></h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($articles)): ?>
                        <form method="GET" action="">
                            <input type="hidden" name="lang" value="<?php echo $lang; ?>">
                            <div class="row">
                                <div class="col-md-8">
                                    <select class="form-select" name="article_id" required>
                                        <option value=""><?php echo $s['select_article']; ?></option>
                                        <?php foreach ($articles as $article): ?>
                                        <option value="<?php echo $article['id']; ?>" <?php echo $articleId == $article['id'] ? 'selected' : ''; ?>><?php echo $article['title']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-check"></i> <?php echo $s['select_article']; ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <?php else: ?>
                        <p><?php echo $s['no_articles']; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($articleId): ?>
                <!-- Upload Article Image -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><?php echo $s['upload_new_image']; ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <input type="hidden" name="article_id" value="<?php echo $articleId; ?>">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="article_image" class="form-label"><?php echo $s['select_image']; ?></label>
                                        <input type="file" class="form-control" id="article_image" name="article_image" accept="image/jpeg, image/png, image/gif" required>
                                        <div class="form-text">
                                            <?php echo $s['allowed_types']; ?><br>
                                            <?php echo $s['max_size']; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" name="upload_article_image" class="btn btn-primary d-block">
                                        <i class="fas fa-upload"></i> <?php echo $s['upload']; ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Article Images -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><?php echo $s['current_images']; ?></h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($articleImages)): ?>
                        <div class="row">
                            <?php foreach ($articleImages as $image): ?>
                            <div class="col-md-4 col-lg-3 mb-4">
                                <div class="card h-100">
                                    <img src="<?php echo '../uploads/articles/' . $image['filename']; ?>" alt="Article Image" class="card-img-top article-image-preview">
                                    <div class="card-body text-center">
                                        <form method="POST" action="">
                                            <input type="hidden" name="article_id" value="<?php echo $articleId; ?>">
                                            <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                                            <button type="submit" name="delete_article_image" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo $s['delete']; ?>?')">
                                                <i class="fas fa-trash"></i> <?php echo $s['delete']; ?>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <p><?php echo $s['no_images']; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
