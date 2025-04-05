<?php
// Include necessary files
require_once 'includes/functions.php';

// Get language and direction
$lang = getCurrentLanguage();
$dir = getLanguageDirection($lang);
$t = getTranslations($lang);

// Set active page
$active = 'about';

// Set page title
$title = $t['about'];

// Get profile data
$profileData = getProfileData($lang);
$profile = $profileData['profile'] ?? null;
$skills = $profileData['skills'] ?? [];
$experiences = $profileData['experiences'] ?? [];
$education = $profileData['education'] ?? [];

// Get contact info
$contactInfo = getContactInfo($lang);

// Get user images
$pdo = getDbConnection();
$stmt = $pdo->query("SELECT id, profile_image, cover_image FROM users LIMIT 1");
$userImages = $stmt->fetch();

// Include header
include 'includes/header.php';
?>

<!-- About Section -->
<section class="py-5">
    <div class="container">
        <h1 class="text-center mb-5"><?php echo $t['about']; ?></h1>
        
        <div class="row mb-5">
            <div class="col-md-4 text-center mb-4 mb-md-0">
                <?php if (!empty($userImages['profile_image'])): ?>
                <img src="uploads/profile/<?php echo $userImages['profile_image']; ?>" alt="<?php echo $profile['name'] ?? ''; ?>" class="img-fluid rounded-circle profile-image-lg">
                <?php else: ?>
                <img src="assets/img/profile-placeholder.jpg" alt="<?php echo $profile['name'] ?? ''; ?>" class="img-fluid rounded-circle profile-image-lg">
                <?php endif; ?>
            </div>
            <div class="col-md-8">
                <h2><?php echo $profile['name'] ?? ''; ?></h2>
                <h3 class="text-muted"><?php echo $profile['title'] ?? ''; ?></h3>
                <div class="mt-4">
                    <?php echo $profile['bio'] ?? ''; ?>
                </div>
            </div>
        </div>
        
        <!-- Skills Section -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="mb-4"><?php echo $t['skills']; ?></h2>
                <?php if (!empty($skills)): ?>
                    <div class="row">
                        <?php foreach ($skills as $skill): ?>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex justify-content-between">
                                <h5><?php echo $skill['name']; ?></h5>
                                <span><?php echo $skill['level']; ?>%</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: <?php echo $skill['level']; ?>%;" aria-valuenow="<?php echo $skill['level']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p><?php echo $t['no_results']; ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Experience Section -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="mb-4"><?php echo $t['experience']; ?></h2>
                <?php if (!empty($experiences)): ?>
                    <div class="timeline">
                        <?php foreach ($experiences as $experience): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h4><?php echo $experience['position']; ?></h4>
                                <h5><?php echo $experience['company']; ?></h5>
                                <p class="text-muted">
                                    <?php 
                                    echo date('M Y', strtotime($experience['start_date'])); 
                                    echo ' - ';
                                    echo $experience['end_date'] ? date('M Y', strtotime($experience['end_date'])) : $t['present'];
                                    ?>
                                </p>
                                <p><?php echo $experience['description']; ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p><?php echo $t['no_results']; ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Education Section -->
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4"><?php echo $t['education']; ?></h2>
                <?php if (!empty($education)): ?>
                    <div class="timeline">
                        <?php foreach ($education as $edu): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h4><?php echo $edu['degree']; ?></h4>
                                <h5><?php echo $edu['institution']; ?></h5>
                                <p class="text-muted">
                                    <?php 
                                    echo date('M Y', strtotime($edu['start_date'])); 
                                    echo ' - ';
                                    echo $edu['end_date'] ? date('M Y', strtotime($edu['end_date'])) : $t['present'];
                                    ?>
                                </p>
                                <p><?php echo $edu['description']; ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p><?php echo $t['no_results']; ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>
