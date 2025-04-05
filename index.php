<?php
// Include necessary files
require_once 'includes/functions.php';

// Get language and direction
$lang = getCurrentLanguage();
$dir = getLanguageDirection($lang);
$t = getTranslations($lang);

// Set active page
$active = 'home';

// Set page title
$title = $t['home'];

// Get profile data
$profileData = getProfileData($lang);
$profile = $profileData['profile'] ?? null;
$skills = $profileData['skills'] ?? [];

// Get latest articles
$articles = getArticles($lang, 3);

// Get latest projects
$projects = getProjects($lang, 3);

// Get contact info
$contactInfo = getContactInfo($lang);

// Get user images
$pdo = getDbConnection();
$stmt = $pdo->query("SELECT id, profile_image, cover_image FROM users LIMIT 1");
$userImages = $stmt->fetch();

// Include header
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 <?php echo $dir === 'rtl' ? 'order-md-2' : ''; ?>">
                <h1 class="display-4 fw-bold"><?php echo $profile['name'] ?? ''; ?></h1>
                <h2 class="lead fs-3"><?php echo $profile['title'] ?? ''; ?></h2>
                <p class="mt-3"><?php echo $profile['bio'] ?? ''; ?></p>
                <div class="mt-4">
                    <a href="about.php?lang=<?php echo $lang; ?>" class="btn btn-primary me-2"><?php echo $t['about']; ?></a>
                    <a href="contact.php?lang=<?php echo $lang; ?>" class="btn btn-outline-primary"><?php echo $t['contact']; ?></a>
                </div>
            </div>
            <div class="col-md-6 <?php echo $dir === 'rtl' ? 'order-md-1' : ''; ?> text-center">
                <?php if (!empty($userImages['profile_image'])): ?>
                <img src="uploads/profile/<?php echo $userImages['profile_image']; ?>" alt="<?php echo $profile['name'] ?? ''; ?>" class="img-fluid rounded-circle profile-image">
                <?php else: ?>
                <img src="assets/img/profile-placeholder.jpg" alt="<?php echo $profile['name'] ?? ''; ?>" class="img-fluid rounded-circle profile-image">
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Skills Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4"><?php echo $t['skills']; ?></h2>
        <div class="row">
            <?php if (!empty($skills)): ?>
                <?php foreach ($skills as $skill): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $skill['name']; ?></h5>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: <?php echo $skill['level']; ?>%;" aria-valuenow="<?php echo $skill['level']; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $skill['level']; ?>%</div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>No skills found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Latest Articles Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4"><?php echo $t['latest_articles']; ?></h2>
        <div class="row">
            <?php if (!empty($articles)): ?>
                <?php foreach ($articles as $article): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php
                        // Get article image
                        $stmt = $pdo->prepare("SELECT filename FROM article_images WHERE article_id = ? LIMIT 1");
                        $stmt->execute([$article['id']]);
                        $articleImage = $stmt->fetch();
                        ?>
                        <?php if (!empty($articleImage)): ?>
                        <img src="uploads/articles/<?php echo $articleImage['filename']; ?>" class="card-img-top article-thumbnail" alt="<?php echo $article['title']; ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $article['title']; ?></h5>
                            <p class="card-text"><?php echo substr($article['summary'], 0, 100) . '...'; ?></p>
                            <a href="article.php?id=<?php echo $article['id']; ?>&lang=<?php echo $lang; ?>" class="btn btn-primary"><?php echo $t['read_more']; ?></a>
                        </div>
                        <div class="card-footer text-muted">
                            <?php echo $t['published_on']; ?>: <?php echo date('Y-m-d', strtotime($article['published_at'])); ?>
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
        <div class="text-center mt-4">
            <a href="articles.php?lang=<?php echo $lang; ?>" class="btn btn-outline-primary"><?php echo $t['articles']; ?></a>
        </div>
    </div>
</section>

<!-- Latest Projects Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4"><?php echo $t['latest_projects']; ?></h2>
        <div class="row">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $project): ?>
                <div class="col-md-4 mb-4">
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
        <div class="text-center mt-4">
            <a href="projects.php?lang=<?php echo $lang; ?>" class="btn btn-outline-primary"><?php echo $t['projects']; ?></a>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>
