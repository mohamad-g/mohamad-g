<?php
// Include necessary files
require_once 'includes/functions.php';

// Get language and direction
$lang = getCurrentLanguage();
$dir = getLanguageDirection($lang);
$t = getTranslations($lang);

// Set active page
$active = 'projects';

// Set page title
$title = $t['projects'];

// Get projects
$projects = getProjects($lang);

// Get contact info
$contactInfo = getContactInfo($lang);

// Get profile data
$profileData = getProfileData($lang);
$profile = $profileData['profile'] ?? null;

// Get database connection
$pdo = getDbConnection();

// Include header
include 'includes/header.php';
?>

<!-- Projects Section -->
<section class="py-5">
    <div class="container">
        <h1 class="text-center mb-5"><?php echo $t['projects']; ?></h1>
        
        <div class="row">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $project): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <?php
                        // Get project image
                        $stmt = $pdo->prepare("SELECT filename FROM project_images WHERE project_id = ? LIMIT 1");
                        $stmt->execute([$project['id']]);
                        $projectImage = $stmt->fetch();
                        ?>
                        <?php if (!empty($projectImage)): ?>
                        <img src="uploads/projects/<?php echo $projectImage['filename']; ?>" class="card-img-top project-thumbnail" alt="<?php echo $project['title']; ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $project['title']; ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted"><?php echo $project['category']; ?></h6>
                            <p class="card-text"><?php echo substr($project['description'], 0, 100) . '...'; ?></p>
                            
                            <?php if (!empty($project['technologies'])): ?>
                            <div class="mb-3">
                                <small class="text-muted"><?php echo $t['technologies_used']; ?>:</small>
                                <div class="mt-1">
                                    <?php foreach ($project['technologies'] as $tech): ?>
                                    <span class="badge bg-secondary me-1"><?php echo $tech['name']; ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <a href="project.php?id=<?php echo $project['id']; ?>&lang=<?php echo $lang; ?>" class="btn btn-primary"><?php echo $t['view_project']; ?></a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p><?php echo $t['no_results']; ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>
