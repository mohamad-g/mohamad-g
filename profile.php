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
        'profile' => 'الملف الشخصي',
        'personal_info' => 'المعلومات الشخصية',
        'name' => 'الاسم',
        'job_title' => 'المسمى الوظيفي',
        'bio' => 'نبذة مختصرة',
        'profile_image' => 'صورة الملف الشخصي',
        'cover_image' => 'صورة الغلاف',
        'current_image' => 'الصورة الحالية',
        'change_image' => 'تغيير الصورة',
        'skills' => 'المهارات',
        'add_skill' => 'إضافة مهارة',
        'skill_name' => 'اسم المهارة',
        'order' => 'الترتيب',
        'actions' => 'الإجراءات',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'save_changes' => 'حفظ التغييرات',
        'cancel' => 'إلغاء',
        'experiences' => 'الخبرات',
        'add_experience' => 'إضافة خبرة',
        'job_title_exp' => 'المسمى الوظيفي',
        'company' => 'اسم الشركة',
        'start_date' => 'تاريخ البدء',
        'end_date' => 'تاريخ الانتهاء',
        'present' => 'حتى الآن',
        'description' => 'الوصف',
        'education' => 'التعليم',
        'add_education' => 'إضافة تعليم',
        'degree' => 'الشهادة',
        'institution' => 'اسم المؤسسة التعليمية',
        'year' => 'السنة',
        'dashboard' => 'لوحة التحكم',
        'articles' => 'المقالات',
        'projects' => 'المشاريع',
        'contact' => 'التواصل',
        'settings' => 'الإعدادات',
        'logout' => 'تسجيل الخروج',
        'change_language' => 'تغيير اللغة',
        'arabic' => 'العربية',
        'german' => 'الألمانية',
        'view_site' => 'عرض الموقع',
        'profile_updated' => 'تم تحديث الملف الشخصي بنجاح',
        'skill_added' => 'تمت إضافة المهارة بنجاح',
        'skill_updated' => 'تم تحديث المهارة بنجاح',
        'skill_deleted' => 'تم حذف المهارة بنجاح',
        'experience_added' => 'تمت إضافة الخبرة بنجاح',
        'experience_updated' => 'تم تحديث الخبرة بنجاح',
        'experience_deleted' => 'تم حذف الخبرة بنجاح',
        'education_added' => 'تمت إضافة التعليم بنجاح',
        'education_updated' => 'تم تحديث التعليم بنجاح',
        'education_deleted' => 'تم حذف التعليم بنجاح',
        'error_occurred' => 'حدث خطأ. يرجى المحاولة مرة أخرى.'
    ],
    'de' => [
        'profile' => 'Profil',
        'personal_info' => 'Persönliche Informationen',
        'name' => 'Name',
        'job_title' => 'Berufsbezeichnung',
        'bio' => 'Kurzbiografie',
        'profile_image' => 'Profilbild',
        'cover_image' => 'Titelbild',
        'current_image' => 'Aktuelles Bild',
        'change_image' => 'Bild ändern',
        'skills' => 'Fähigkeiten',
        'add_skill' => 'Fähigkeit hinzufügen',
        'skill_name' => 'Name der Fähigkeit',
        'order' => 'Reihenfolge',
        'actions' => 'Aktionen',
        'edit' => 'Bearbeiten',
        'delete' => 'Löschen',
        'save_changes' => 'Änderungen speichern',
        'cancel' => 'Abbrechen',
        'experiences' => 'Berufserfahrung',
        'add_experience' => 'Berufserfahrung hinzufügen',
        'job_title_exp' => 'Berufsbezeichnung',
        'company' => 'Unternehmen',
        'start_date' => 'Startdatum',
        'end_date' => 'Enddatum',
        'present' => 'Bis heute',
        'description' => 'Beschreibung',
        'education' => 'Ausbildung',
        'add_education' => 'Ausbildung hinzufügen',
        'degree' => 'Abschluss',
        'institution' => 'Bildungseinrichtung',
        'year' => 'Jahr',
        'dashboard' => 'Dashboard',
        'articles' => 'Artikel',
        'projects' => 'Projekte',
        'contact' => 'Kontakt',
        'settings' => 'Einstellungen',
        'logout' => 'Abmelden',
        'change_language' => 'Sprache ändern',
        'arabic' => 'Arabisch',
        'german' => 'Deutsch',
        'view_site' => 'Website anzeigen',
        'profile_updated' => 'Profil erfolgreich aktualisiert',
        'skill_added' => 'Fähigkeit erfolgreich hinzugefügt',
        'skill_updated' => 'Fähigkeit erfolgreich aktualisiert',
        'skill_deleted' => 'Fähigkeit erfolgreich gelöscht',
        'experience_added' => 'Berufserfahrung erfolgreich hinzugefügt',
        'experience_updated' => 'Berufserfahrung erfolgreich aktualisiert',
        'experience_deleted' => 'Berufserfahrung erfolgreich gelöscht',
        'education_added' => 'Ausbildung erfolgreich hinzugefügt',
        'education_updated' => 'Ausbildung erfolgreich aktualisiert',
        'education_deleted' => 'Ausbildung erfolgreich gelöscht',
        'error_occurred' => 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.'
    ]
];

// Get strings for current language
$s = $strings[$lang];

// Process form submission
$message = '';
$messageType = '';

// Get profile data
try {
    $stmt = $pdo->prepare("SELECT * FROM profile WHERE user_id = ? AND language = ?");
    $stmt->execute([$user['id'], $lang]);
    $profile = $stmt->fetch();
    
    // Get skills
    $stmt = $pdo->prepare("SELECT * FROM skills WHERE user_id = ? AND language = ? ORDER BY `order` ASC");
    $stmt->execute([$user['id'], $lang]);
    $skills = $stmt->fetchAll();
    
    // Get experiences
    $stmt = $pdo->prepare("SELECT * FROM experiences WHERE user_id = ? AND language = ? ORDER BY start_date DESC");
    $stmt->execute([$user['id'], $lang]);
    $experiences = $stmt->fetchAll();
    
    // Get education
    $stmt = $pdo->prepare("SELECT * FROM education WHERE user_id = ? AND language = ? ORDER BY year DESC");
    $stmt->execute([$user['id'], $lang]);
    $education = $stmt->fetchAll();
} catch (PDOException $e) {
    $message = $s['error_occurred'];
    $messageType = 'danger';
}

// Update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'] ?? '';
    $jobTitle = $_POST['job_title'] ?? '';
    $bio = $_POST['bio'] ?? '';
    
    try {
        if ($profile) {
            // Update existing profile
            $stmt = $pdo->prepare("UPDATE profile SET name = ?, job_title = ?, bio = ? WHERE user_id = ? AND language = ?");
            $stmt->execute([$name, $jobTitle, $bio, $user['id'], $lang]);
        } else {
            // Insert new profile
            $stmt = $pdo->prepare("INSERT INTO profile (user_id, language, name, job_title, bio) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$user['id'], $lang, $name, $jobTitle, $bio]);
        }
        
        $message = $s['profile_updated'];
        $messageType = 'success';
        
        // Refresh profile data
        $stmt = $pdo->prepare("SELECT * FROM profile WHERE user_id = ? AND language = ?");
        $stmt->execute([$user['id'], $lang]);
        $profile = $stmt->fetch();
    } catch (PDOException $e) {
        $message = $s['error_occurred'];
        $messageType = 'danger';
    }
}

// Add skill
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_skill'])) {
    $skillName = $_POST['skill_name'] ?? '';
    $order = $_POST['order'] ?? 0;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO skills (user_id, language, name, `order`) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user['id'], $lang, $skillName, $order]);
        
        $message = $s['skill_added'];
        $messageType = 'success';
        
        // Refresh skills data
        $stmt = $pdo->prepare("SELECT * FROM skills WHERE user_id = ? AND language = ? ORDER BY `order` ASC");
        $stmt->execute([$user['id'], $lang]);
        $skills = $stmt->fetchAll();
    } catch (PDOException $e) {
        $message = $s['error_occurred'];
        $messageType = 'danger';
    }
}

// Update skill
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_skill'])) {
    $skillId = $_POST['skill_id'] ?? 0;
    $skillName = $_POST['skill_name'] ?? '';
    $order = $_POST['order'] ?? 0;
    
    try {
        $stmt = $pdo->prepare("UPDATE skills SET name = ?, `order` = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$skillName, $order, $skillId, $user['id']]);
        
        $message = $s['skill_updated'];
        $messageType = 'success';
        
        // Refresh skills data
        $stmt = $pdo->prepare("SELECT * FROM skills WHERE user_id = ? AND language = ? ORDER BY `order` ASC");
        $stmt->execute([$user['id'], $lang]);
        $skills = $stmt->fetchAll();
    } catch (PDOException $e) {
        $message = $s['error_occurred'];
        $messageType = 'danger';
    }
}

// Delete skill
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_skill'])) {
    $skillId = $_POST['skill_id'] ?? 0;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM skills WHERE id = ? AND user_id = ?");
        $stmt->execute([$skillId, $user['id']]);
        
        $message = $s['skill_deleted'];
        $messageType = 'success';
        
        // Refresh skills data
        $stmt = $pdo->prepare("SELECT * FROM skills WHERE user_id = ? AND language = ? ORDER BY `order` ASC");
        $stmt->execute([$user['id'], $lang]);
        $skills = $stmt->fetchAll();
    } catch (PDOException $e) {
        $message = $s['error_occurred'];
        $messageType = 'danger';
    }
}

// Add experience
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_experience'])) {
    $title = $_POST['title'] ?? '';
    $company = $_POST['company'] ?? '';
    $startDate = $_POST['start_date'] ?? '';
    $endDate = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $description = $_POST['description'] ?? '';
    $order = $_POST['order'] ?? 0;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO experiences (user_id, language, title, company, start_date, end_date, description, `order`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user['id'], $lang, $title, $company, $startDate, $endDate, $description, $order]);
        
        $message = $s['experience_added'];
        $messageType = 'success';
        
        // Refresh experiences data
        $stmt = $pdo->prepare("SELECT * FROM experiences WHERE user_id = ? AND language = ? ORDER BY start_date DESC");
        $stmt->execute([$user['id'], $lang]);
        $experiences = $stmt->fetchAll();
    } catch (PDOException $e) {
        $message = $s['error_occurred'];
        $messageType = 'danger';
    }
}

// Update experience
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_experience'])) {
    $experienceId = $_POST['experience_id'] ?? 0;
    $title = $_POST['title'] ?? '';
    $company = $_POST['company'] ?? '';
    $startDate = $_POST['start_date'] ?? '';
    $endDate = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $description = $_POST['description'] ?? '';
    $order = $_POST['order'] ?? 0;
    
    try {
        $stmt = $pdo->prepare("UPDATE experiences SET title = ?, company = ?, start_date = ?, end_date = ?, description = ?, `order` = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$title, $company, $startDate, $endDate, $description, $order, $experienceId, $user['id']]);
        
        $message = $s['experience_updated'];
        $messageType = 'success';
        
        // Refresh experiences data
        $stmt = $pdo->prepare("SELECT * FROM experiences WHERE user_id = ? AND language = ? ORDER BY start_date DESC");
        $stmt->execute([$user['id'], $lang]);
        $experiences = $stmt->fetchAll();
    } catch (PDOException $e) {
        $message = $s['error_occurred'];
        $messageType = 'danger';
    }
}

// Delete experience
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_experience'])) {
    $experienceId = $_POST['experience_id'] ?? 0;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM experiences WHERE id = ? AND user_id = ?");
        $stmt->execute([$experienceId, $user['id']]);
        
        $message = $s['experience_deleted'];
        $messageType = 'success';
        
        // Refresh experiences data
        $stmt = $pdo->prepare("SELECT * FROM experiences WHERE user_id = ? AND language = ? ORDER BY start_date DESC");
        $stmt->execute([$user['id'], $lang]);
        $experiences = $stmt->fetchAll();
    } catch (PDOException $e) {
        $message = $s['error_occurred'];
        $messageType = 'danger';
    }
}

// Add education
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_education'])) {
    $degree = $_POST['degree'] ?? '';
    $institution = $_POST['institution'] ?? '';
    $year = $_POST['year'] ?? '';
    $description = $_POST['description'] ?? '';
    $order = $_POST['order'] ?? 0;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO education (user_id, language, degree, institution, year, description, `order`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user['id'], $lang, $degree, $institution, $year, $description, $order]);
        
        $message = $s['education_added'];
        $messageType = 'success';
        
        // Refresh education data
        $stmt = $pdo->prepare("SELECT * FROM education WHERE user_id = ? AND language = ? ORDER BY year DESC");
        $stmt->execute([$user['id'], $lang]);
        $education = $stmt->fetchAll();
    } catch (PDOException $e) {
        $message = $s['error_occurred'];
        $messageType = 'danger';
    }
}

// Update education
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_education'])) {
    $educationId = $_POST['education_id'] ?? 0;
    $degree = $_POST['degree'] ?? '';
    $institution = $_POST['institution'] ?? '';
    $year = $_POST['year'] ?? '';
    $description = $_POST['description'] ?? '';
    $order = $_POST['order'] ?? 0;
    
    try {
        $stmt = $pdo->prepare("UPDATE education SET degree = ?, institution = ?, year = ?, description = ?, `order` = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$degree, $institution, $year, $description, $order, $educationId, $user['id']]);
        
        $message = $s['education_updated'];
        $messageType = 'success';
        
        // Refresh education data
        $stmt = $pdo->prepare("SELECT * FROM education WHERE user_id = ? AND language = ? ORDER BY year DESC");
        $stmt->execute([$user['id'], $lang]);
        $education = $stmt->fetchAll();
    } catch (PDOException $e) {
        $message = $s['error_occurred'];
        $messageType = 'danger';
    }
}

// Delete education
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_education'])) {
    $educationId = $_POST['education_id'] ?? 0;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM education WHERE id = ? AND user_id = ?");
        $stmt->execute([$educationId, $user['id']]);
        
        $message = $s['education_deleted'];
        $messageType = 'success';
        
        // Refresh education data
        $stmt = $pdo->prepare("SELECT * FROM education WHERE user_id = ? AND language = ? ORDER BY year DESC");
        $stmt->execute([$user['id'], $lang]);
        $education = $stmt->fetchAll();
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
    <title><?php echo $s['profile']; ?></title>
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
                            <a class="nav-link active" href="profile.php">
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
                    <h1 class="h2"><?php echo $s['profile']; ?></h1>
                </div>
                
                <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Personal Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><?php echo $s['personal_info']; ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label"><?php echo $s['name']; ?></label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($profile['name'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="job_title" class="form-label"><?php echo $s['job_title']; ?></label>
                                <input type="text" class="form-control" id="job_title" name="job_title" value="<?php echo htmlspecialchars($profile['job_title'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="bio" class="form-label"><?php echo $s['bio']; ?></label>
                                <textarea class="form-control" id="bio" name="bio" rows="4"><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></textarea>
                            </div>
                            <button type="submit" name="update_profile" class="btn btn-primary"><?php echo $s['save_changes']; ?></button>
                        </form>
                    </div>
                </div>
                
                <!-- Skills -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><?php echo $s['skills']; ?></h5>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addSkillModal">
                            <i class="fas fa-plus"></i> <?php echo $s['add_skill']; ?>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th><?php echo $s['skill_name']; ?></th>
                                        <th><?php echo $s['order']; ?></th>
                                        <th><?php echo $s['actions']; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($skills)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center">No skills found</td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($skills as $skill): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($skill['name']); ?></td>
                                            <td><?php echo htmlspecialchars($skill['order']); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editSkillModal<?php echo $skill['id']; ?>">
                                                    <i class="fas fa-edit"></i> <?php echo $s['edit']; ?>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteSkillModal<?php echo $skill['id']; ?>">
                                                    <i class="fas fa-trash"></i> <?php echo $s['delete']; ?>
                                                </button>
                                                
                                                <!-- Edit Skill Modal -->
                                                <div class="modal fade" id="editSkillModal<?php echo $skill['id']; ?>" tabindex="-1" aria-labelledby="editSkillModalLabel<?php echo $skill['id']; ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editSkillModalLabel<?php echo $skill['id']; ?>"><?php echo $s['edit']; ?> <?php echo $s['skill_name']; ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form method="POST" action="">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="skill_id" value="<?php echo $skill['id']; ?>">
                                                                    <div class="mb-3">
                                                                        <label for="skill_name<?php echo $skill['id']; ?>" class="form-label"><?php echo $s['skill_name']; ?></label>
                                                                        <input type="text" class="form-control" id="skill_name<?php echo $skill['id']; ?>" name="skill_name" value="<?php echo htmlspecialchars($skill['name']); ?>" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="order<?php echo $skill['id']; ?>" class="form-label"><?php echo $s['order']; ?></label>
                                                                        <input type="number" class="form-control" id="order<?php echo $skill['id']; ?>" name="order" value="<?php echo htmlspecialchars($skill['order']); ?>" required>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $s['cancel']; ?></button>
                                                                    <button type="submit" name="update_skill" class="btn btn-primary"><?php echo $s['save_changes']; ?></button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Delete Skill Modal -->
                                                <div class="modal fade" id="deleteSkillModal<?php echo $skill['id']; ?>" tabindex="-1" aria-labelledby="deleteSkillModalLabel<?php echo $skill['id']; ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteSkillModalLabel<?php echo $skill['id']; ?>"><?php echo $s['delete']; ?> <?php echo $s['skill_name']; ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to delete this skill?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $s['cancel']; ?></button>
                                                                <form method="POST" action="">
                                                                    <input type="hidden" name="skill_id" value="<?php echo $skill['id']; ?>">
                                                                    <button type="submit" name="delete_skill" class="btn btn-danger"><?php echo $s['delete']; ?></button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Experiences -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><?php echo $s['experiences']; ?></h5>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addExperienceModal">
                            <i class="fas fa-plus"></i> <?php echo $s['add_experience']; ?>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th><?php echo $s['job_title_exp']; ?></th>
                                        <th><?php echo $s['company']; ?></th>
                                        <th><?php echo $s['start_date']; ?></th>
                                        <th><?php echo $s['end_date']; ?></th>
                                        <th><?php echo $s['actions']; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($experiences)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No experiences found</td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($experiences as $experience): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($experience['title']); ?></td>
                                            <td><?php echo htmlspecialchars($experience['company']); ?></td>
                                            <td><?php echo htmlspecialchars($experience['start_date']); ?></td>
                                            <td><?php echo $experience['end_date'] ? htmlspecialchars($experience['end_date']) : $s['present']; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editExperienceModal<?php echo $experience['id']; ?>">
                                                    <i class="fas fa-edit"></i> <?php echo $s['edit']; ?>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteExperienceModal<?php echo $experience['id']; ?>">
                                                    <i class="fas fa-trash"></i> <?php echo $s['delete']; ?>
                                                </button>
                                                
                                                <!-- Edit Experience Modal -->
                                                <div class="modal fade" id="editExperienceModal<?php echo $experience['id']; ?>" tabindex="-1" aria-labelledby="editExperienceModalLabel<?php echo $experience['id']; ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editExperienceModalLabel<?php echo $experience['id']; ?>"><?php echo $s['edit']; ?> <?php echo $s['experiences']; ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form method="POST" action="">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="experience_id" value="<?php echo $experience['id']; ?>">
                                                                    <div class="mb-3">
                                                                        <label for="title<?php echo $experience['id']; ?>" class="form-label"><?php echo $s['job_title_exp']; ?></label>
                                                                        <input type="text" class="form-control" id="title<?php echo $experience['id']; ?>" name="title" value="<?php echo htmlspecialchars($experience['title']); ?>" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="company<?php echo $experience['id']; ?>" class="form-label"><?php echo $s['company']; ?></label>
                                                                        <input type="text" class="form-control" id="company<?php echo $experience['id']; ?>" name="company" value="<?php echo htmlspecialchars($experience['company']); ?>" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="start_date<?php echo $experience['id']; ?>" class="form-label"><?php echo $s['start_date']; ?></label>
                                                                        <input type="date" class="form-control" id="start_date<?php echo $experience['id']; ?>" name="start_date" value="<?php echo htmlspecialchars($experience['start_date']); ?>" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="end_date<?php echo $experience['id']; ?>" class="form-label"><?php echo $s['end_date']; ?></label>
                                                                        <input type="date" class="form-control" id="end_date<?php echo $experience['id']; ?>" name="end_date" value="<?php echo htmlspecialchars($experience['end_date'] ?? ''); ?>">
                                                                        <small class="form-text text-muted">Leave empty for current position</small>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="description<?php echo $experience['id']; ?>" class="form-label"><?php echo $s['description']; ?></label>
                                                                        <textarea class="form-control" id="description<?php echo $experience['id']; ?>" name="description" rows="3"><?php echo htmlspecialchars($experience['description']); ?></textarea>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="order<?php echo $experience['id']; ?>" class="form-label"><?php echo $s['order']; ?></label>
                                                                        <input type="number" class="form-control" id="order<?php echo $experience['id']; ?>" name="order" value="<?php echo htmlspecialchars($experience['order']); ?>" required>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $s['cancel']; ?></button>
                                                                    <button type="submit" name="update_experience" class="btn btn-primary"><?php echo $s['save_changes']; ?></button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Delete Experience Modal -->
                                                <div class="modal fade" id="deleteExperienceModal<?php echo $experience['id']; ?>" tabindex="-1" aria-labelledby="deleteExperienceModalLabel<?php echo $experience['id']; ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteExperienceModalLabel<?php echo $experience['id']; ?>"><?php echo $s['delete']; ?> <?php echo $s['experiences']; ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to delete this experience?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $s['cancel']; ?></button>
                                                                <form method="POST" action="">
                                                                    <input type="hidden" name="experience_id" value="<?php echo $experience['id']; ?>">
                                                                    <button type="submit" name="delete_experience" class="btn btn-danger"><?php echo $s['delete']; ?></button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Education -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><?php echo $s['education']; ?></h5>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addEducationModal">
                            <i class="fas fa-plus"></i> <?php echo $s['add_education']; ?>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th><?php echo $s['degree']; ?></th>
                                        <th><?php echo $s['institution']; ?></th>
                                        <th><?php echo $s['year']; ?></th>
                                        <th><?php echo $s['actions']; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($education)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No education found</td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($education as $edu): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($edu['degree']); ?></td>
                                            <td><?php echo htmlspecialchars($edu['institution']); ?></td>
                                            <td><?php echo htmlspecialchars($edu['year']); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editEducationModal<?php echo $edu['id']; ?>">
                                                    <i class="fas fa-edit"></i> <?php echo $s['edit']; ?>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteEducationModal<?php echo $edu['id']; ?>">
                                                    <i class="fas fa-trash"></i> <?php echo $s['delete']; ?>
                                                </button>
                                                
                                                <!-- Edit Education Modal -->
                                                <div class="modal fade" id="editEducationModal<?php echo $edu['id']; ?>" tabindex="-1" aria-labelledby="editEducationModalLabel<?php echo $edu['id']; ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editEducationModalLabel<?php echo $edu['id']; ?>"><?php echo $s['edit']; ?> <?php echo $s['education']; ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form method="POST" action="">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="education_id" value="<?php echo $edu['id']; ?>">
                                                                    <div class="mb-3">
                                                                        <label for="degree<?php echo $edu['id']; ?>" class="form-label"><?php echo $s['degree']; ?></label>
                                                                        <input type="text" class="form-control" id="degree<?php echo $edu['id']; ?>" name="degree" value="<?php echo htmlspecialchars($edu['degree']); ?>" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="institution<?php echo $edu['id']; ?>" class="form-label"><?php echo $s['institution']; ?></label>
                                                                        <input type="text" class="form-control" id="institution<?php echo $edu['id']; ?>" name="institution" value="<?php echo htmlspecialchars($edu['institution']); ?>" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="year<?php echo $edu['id']; ?>" class="form-label"><?php echo $s['year']; ?></label>
                                                                        <input type="number" class="form-control" id="year<?php echo $edu['id']; ?>" name="year" value="<?php echo htmlspecialchars($edu['year']); ?>" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="description<?php echo $edu['id']; ?>" class="form-label"><?php echo $s['description']; ?></label>
                                                                        <textarea class="form-control" id="description<?php echo $edu['id']; ?>" name="description" rows="3"><?php echo htmlspecialchars($edu['description']); ?></textarea>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="order<?php echo $edu['id']; ?>" class="form-label"><?php echo $s['order']; ?></label>
                                                                        <input type="number" class="form-control" id="order<?php echo $edu['id']; ?>" name="order" value="<?php echo htmlspecialchars($edu['order']); ?>" required>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $s['cancel']; ?></button>
                                                                    <button type="submit" name="update_education" class="btn btn-primary"><?php echo $s['save_changes']; ?></button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Delete Education Modal -->
                                                <div class="modal fade" id="deleteEducationModal<?php echo $edu['id']; ?>" tabindex="-1" aria-labelledby="deleteEducationModalLabel<?php echo $edu['id']; ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteEducationModalLabel<?php echo $edu['id']; ?>"><?php echo $s['delete']; ?> <?php echo $s['education']; ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to delete this education?</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $s['cancel']; ?></button>
                                                                <form method="POST" action="">
                                                                    <input type="hidden" name="education_id" value="<?php echo $edu['id']; ?>">
                                                                    <button type="submit" name="delete_education" class="btn btn-danger"><?php echo $s['delete']; ?></button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <!-- Add Skill Modal -->
    <div class="modal fade" id="addSkillModal" tabindex="-1" aria-labelledby="addSkillModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSkillModalLabel"><?php echo $s['add_skill']; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="skill_name" class="form-label"><?php echo $s['skill_name']; ?></label>
                            <input type="text" class="form-control" id="skill_name" name="skill_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="order" class="form-label"><?php echo $s['order']; ?></label>
                            <input type="number" class="form-control" id="order" name="order" value="0" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $s['cancel']; ?></button>
                        <button type="submit" name="add_skill" class="btn btn-primary"><?php echo $s['add_skill']; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Add Experience Modal -->
    <div class="modal fade" id="addExperienceModal" tabindex="-1" aria-labelledby="addExperienceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addExperienceModalLabel"><?php echo $s['add_experience']; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label"><?php echo $s['job_title_exp']; ?></label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="company" class="form-label"><?php echo $s['company']; ?></label>
                            <input type="text" class="form-control" id="company" name="company" required>
                        </div>
                        <div class="mb-3">
                            <label for="start_date" class="form-label"><?php echo $s['start_date']; ?></label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label"><?php echo $s['end_date']; ?></label>
                            <input type="date" class="form-control" id="end_date" name="end_date">
                            <small class="form-text text-muted">Leave empty for current position</small>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label"><?php echo $s['description']; ?></label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="order" class="form-label"><?php echo $s['order']; ?></label>
                            <input type="number" class="form-control" id="order" name="order" value="0" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $s['cancel']; ?></button>
                        <button type="submit" name="add_experience" class="btn btn-primary"><?php echo $s['add_experience']; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Add Education Modal -->
    <div class="modal fade" id="addEducationModal" tabindex="-1" aria-labelledby="addEducationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEducationModalLabel"><?php echo $s['add_education']; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="degree" class="form-label"><?php echo $s['degree']; ?></label>
                            <input type="text" class="form-control" id="degree" name="degree" required>
                        </div>
                        <div class="mb-3">
                            <label for="institution" class="form-label"><?php echo $s['institution']; ?></label>
                            <input type="text" class="form-control" id="institution" name="institution" required>
                        </div>
                        <div class="mb-3">
                            <label for="year" class="form-label"><?php echo $s['year']; ?></label>
                            <input type="number" class="form-control" id="year" name="year" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label"><?php echo $s['description']; ?></label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="order" class="form-label"><?php echo $s['order']; ?></label>
                            <input type="number" class="form-control" id="order" name="order" value="0" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo $s['cancel']; ?></button>
                        <button type="submit" name="add_education" class="btn btn-primary"><?php echo $s['add_education']; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
