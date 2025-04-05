<?php
// Include necessary files
require_once 'includes/functions.php';

// Get language and direction
$lang = getCurrentLanguage();
$dir = getLanguageDirection($lang);
$t = getTranslations($lang);

// Get project ID
$projectId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get project
$project = getProjectById($projectId, $lang);

// Redirect if project not found
if (!$project) {
    header("Location: projects.php?lang=$lang");
    exit;
}

// Set active page
$active = 'projects';

// Set page title
$title = $project['title'];

// Get contact info
$contactInfo = getContactInfo($lang);

// Get profile data
$profileData = getProfileData($lang);
$profile = $profileData['profile'] ?? null;

// Get project images
$pdo = getDbConnection();
$stmt = $pdo->prepare("SELECT filename FROM project_images WHERE project_id = ? ORDER BY id DESC");
$stmt->execute([$projectId]);
$projectImages = $stmt->fetchAll();

// Include header
include 'includes/header.php';
?>

<!-- Project Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <article>
                    <header class="mb-4">
                        <h1 class="fw-bold"><?php echo $project['title']; ?></h1>
                        <h5 class="text-muted"><?php echo $project['category']; ?></h5>
                    </header>
                    
                    <?php if (!empty($projectImages)): ?>
                    <div class="project-featured-image mb-4">
                        <img src="uploads/projects/<?php echo $projectImages[0]['filename']; ?>" alt="<?php echo $project['title']; ?>" class="img-fluid rounded">
                    </div>
                    <?php endif; ?>
                    
                    <div class="project-content mb-4">
                        <?php echo $project['description']; ?>
                    </div>
                    
                    <?php if (!empty($project['technologies'])): ?>
                    <div class="mb-4">
                        <h4><?php echo $t['technologies_used']; ?>:</h4>
                        <div class="mt-2">
                            <?php foreach ($project['technologies'] as $tech): ?>
                            <span class="badge bg-secondary me-1 mb-1 p-2"><?php echo $tech['name']; ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (count($projectImages) > 1): ?>
                    <div class="project-gallery mt-4 mb-4">
                        <h4><?php echo $t['project_images']; ?></h4>
                        <div class="row">
                            <?php foreach (array_slice($projectImages, 1) as $image): ?>
                            <div class="col-md-4 mb-3">
                                <img src="uploads/projects/<?php echo $image['filename']; ?>" alt="<?php echo $project['title']; ?>" class="img-fluid rounded">
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($project['url'])): ?>
                    <div class="mb-4">
                        <a href="<?php echo $project['url']; ?>" target="_blank" class="btn btn-success">
                            <i class="fas fa-external-link-alt"></i> <?php echo $t['view_project']; ?>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mt-5">
                        <a href="projects.php?lang=<?php echo $lang; ?>" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-<?php echo $dir === 'rtl' ? 'right' : 'left'; ?>"></i> 
                            <?php echo $t['projects']; ?>
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>
