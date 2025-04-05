<?php
// Include necessary files
require_once 'includes/functions.php';

// Get language and direction
$lang = getCurrentLanguage();
$dir = getLanguageDirection($lang);
$t = getTranslations($lang);

// Set active page
$active = 'articles';

// Set page title
$title = $t['articles'];

// Get articles
$articles = getArticles($lang);

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

<!-- Articles Section -->
<section class="py-5">
    <div class="container">
        <h1 class="text-center mb-5"><?php echo $t['articles']; ?></h1>
        
        <div class="row">
            <?php if (!empty($articles)): ?>
                <?php foreach ($articles as $article): ?>
                <div class="col-md-6 col-lg-4 mb-4">
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
                            <p class="card-text"><?php echo $article['summary']; ?></p>
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
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>
