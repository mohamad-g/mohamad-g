<?php
// Include necessary files
require_once 'includes/functions.php';

// Get language and direction
$lang = getCurrentLanguage();
$dir = getLanguageDirection($lang);
$t = getTranslations($lang);

// Get article ID
$articleId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get article
$article = getArticleById($articleId, $lang);

// Redirect if article not found
if (!$article) {
    header("Location: articles.php?lang=$lang");
    exit;
}

// Set active page
$active = 'articles';

// Set page title
$title = $article['title'];

// Get contact info
$contactInfo = getContactInfo($lang);

// Get profile data
$profileData = getProfileData($lang);
$profile = $profileData['profile'] ?? null;

// Get article images
$pdo = getDbConnection();
$stmt = $pdo->prepare("SELECT filename FROM article_images WHERE article_id = ? ORDER BY id DESC");
$stmt->execute([$articleId]);
$articleImages = $stmt->fetchAll();

// Include header
include 'includes/header.php';
?>

<!-- Article Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <article>
                    <header class="mb-4">
                        <h1 class="fw-bold"><?php echo $article['title']; ?></h1>
                        <div class="text-muted mb-3">
                            <?php echo $t['published_on']; ?>: <?php echo date('Y-m-d', strtotime($article['published_at'])); ?>
                        </div>
                    </header>
                    
                    <?php if (!empty($articleImages)): ?>
                    <div class="article-featured-image mb-4">
                        <img src="uploads/articles/<?php echo $articleImages[0]['filename']; ?>" alt="<?php echo $article['title']; ?>" class="img-fluid rounded">
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($article['summary'])): ?>
                    <div class="lead mb-4">
                        <?php echo $article['summary']; ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="article-content">
                        <?php echo $article['content']; ?>
                    </div>
                    
                    <?php if (count($articleImages) > 1): ?>
                    <div class="article-gallery mt-4 mb-4">
                        <h4><?php echo $t['article_images']; ?></h4>
                        <div class="row">
                            <?php foreach (array_slice($articleImages, 1) as $image): ?>
                            <div class="col-md-4 mb-3">
                                <img src="uploads/articles/<?php echo $image['filename']; ?>" alt="<?php echo $article['title']; ?>" class="img-fluid rounded">
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="mt-5">
                        <a href="articles.php?lang=<?php echo $lang; ?>" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-<?php echo $dir === 'rtl' ? 'right' : 'left'; ?>"></i> 
                            <?php echo $t['articles']; ?>
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
