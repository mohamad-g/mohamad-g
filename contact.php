<?php
// Include necessary files
require_once 'includes/functions.php';

// Get language and direction
$lang = getCurrentLanguage();
$dir = getLanguageDirection($lang);
$t = getTranslations($lang);

// Set active page
$active = 'contact';

// Set page title
$title = $t['contact'];

// Get contact info
$contactInfo = getContactInfo($lang);
$contact = $contactInfo['contact'] ?? null;
$socialMedia = $contactInfo['social_media'] ?? [];

// Get profile data
$profileData = getProfileData($lang);
$profile = $profileData['profile'] ?? null;

// Get user images
$pdo = getDbConnection();
$stmt = $pdo->query("SELECT id, profile_image, cover_image FROM users LIMIT 1");
$userImages = $stmt->fetch();

// Include header
include 'includes/header.php';
?>

<!-- Contact Section -->
<section class="py-5">
    <div class="container">
        <h1 class="text-center mb-5"><?php echo $t['contact_me']; ?></h1>
        
        <div class="row">
            <div class="col-md-4 mb-4 text-center">
                <?php if (!empty($userImages['profile_image'])): ?>
                <img src="uploads/profile/<?php echo $userImages['profile_image']; ?>" alt="<?php echo $profile['name'] ?? ''; ?>" class="img-fluid rounded-circle profile-image-md mb-3">
                <?php else: ?>
                <img src="assets/img/profile-placeholder.jpg" alt="<?php echo $profile['name'] ?? ''; ?>" class="img-fluid rounded-circle profile-image-md mb-3">
                <?php endif; ?>
                <h3><?php echo $profile['name'] ?? ''; ?></h3>
                <p class="text-muted"><?php echo $profile['title'] ?? ''; ?></p>
            </div>
            
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h3 class="card-title"><?php echo $t['contact_info']; ?></h3>
                                
                                <?php if ($contact): ?>
                                <ul class="list-unstyled mt-4">
                                    <?php if (!empty($contact['phone'])): ?>
                                    <li class="mb-3">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-phone fa-2x text-primary"></i>
                                            </div>
                                            <div class="ms-3">
                                                <h5><?php echo $t['phone']; ?></h5>
                                                <p><?php echo $contact['phone']; ?></p>
                                            </div>
                                        </div>
                                    </li>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($contact['email'])): ?>
                                    <li class="mb-3">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-envelope fa-2x text-primary"></i>
                                            </div>
                                            <div class="ms-3">
                                                <h5><?php echo $t['email']; ?></h5>
                                                <p><a href="mailto:<?php echo $contact['email']; ?>"><?php echo $contact['email']; ?></a></p>
                                            </div>
                                        </div>
                                    </li>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($contact['address'])): ?>
                                    <li class="mb-3">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                                            </div>
                                            <div class="ms-3">
                                                <h5><?php echo $t['address']; ?></h5>
                                                <p><?php echo nl2br($contact['address']); ?></p>
                                            </div>
                                        </div>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                                <?php else: ?>
                                <p><?php echo $t['no_results']; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h3 class="card-title"><?php echo $t['social_media']; ?></h3>
                                
                                <?php if (!empty($socialMedia)): ?>
                                <div class="social-icons mt-4">
                                    <?php foreach ($socialMedia as $social): ?>
                                    <a href="<?php echo $social['url']; ?>" target="_blank" class="social-icon-link">
                                        <div class="social-icon mb-3">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <i class="fab <?php echo $social['icon']; ?> fa-2x text-primary"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h5><?php echo isset($platforms[$social['platform']]) ? $platforms[$social['platform']] : $social['platform']; ?></h5>
                                                    <p class="text-muted"><?php echo $social['url']; ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <p><?php echo $t['no_results']; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>
