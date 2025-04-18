<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Handle delete
if (isset($_GET['delete'])) {
    $postId = $_GET['delete'];

    // Get image URL to delete the file from folder
    $stmt = $pdo->prepare("SELECT image_url FROM posts WHERE id = :id");
    $stmt->execute(['id' => $postId]);
    $post = $stmt->fetch();

    // Delete the image file from the folder
    if ($post && $post['image_url']) {
        $imagePath = __DIR__ . '/../' . $post['image_url'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    // Delete post from DB
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :id");
    $stmt->execute(['id' => $postId]);

    header("Location: dashboard.php");
    exit;
}

// Fetch posts
$posts = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC")->fetchAll();
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">

<div class="container">
    <h2>Admin Dashboard</h2>
    <a href="create-post.php">+ New Post</a> | <a href="logout.php">Logout</a>

    <hr>
    <h3>All Posts</h3>

    <?php foreach ($posts as $post): ?>
        <div class="post-box">
            <h4><?= htmlspecialchars($post['title']) ?></h4>
            <?php if ($post['image_url']): ?>
                <img src="<?= BASE_URL . $post['image_url'] ?>" width="150">
            <?php endif; ?>
            <p><?= substr(strip_tags($post['content']), 0, 100) ?>...</p>
            <a href="<?= BASE_URL ?>/posts/post.php?id=<?= $post['id'] ?>">View</a> |
            <a href="dashboard.php?delete=<?= $post['id'] ?>" onclick="return confirm('Delete this post?')">Delete</a>
        </div>
        <hr>
    <?php endforeach; ?>
</div>
</body>
</html>
