<?php
require_once __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/header.php';

$stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
$posts = $stmt->fetchAll();
?>

<div class="container">
    <h2>Latest Blog Posts</h2>
    <?php foreach ($posts as $post): ?>
        <div style="margin-bottom: 30px;">
            <h3><a href="<?php echo BASE_URL . '/posts/post.php?id=' . $post['id']; ?>">
                <?= htmlspecialchars($post['title']) ?>
            </a></h3>
            <small>Published on <?= $post['created_at'] ?></small>
            <?php if (!empty($post['image_url'])): ?>
                <div><img src="<?= htmlspecialchars($post['image_url']) ?>" alt="Post Image" class="post-image">
</div>
            <?php endif; ?>
            <p><?= nl2br(htmlspecialchars(substr($post['content'], 0, 200))) ?>...</p>
            <a href="<?php echo BASE_URL . '/posts/post.php?id=' . $post['id']; ?>">Read more</a>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
