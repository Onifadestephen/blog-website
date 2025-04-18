<?php
require_once __DIR__ . '/../includes/db.php';
include __DIR__ . '/../includes/header.php';

$post_id = $_GET['id'] ?? null;
if (!$post_id) {
    echo "<p>Invalid post ID</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? 'Anonymous';
    $email = $_POST['email'] ?? '';
    $comment = $_POST['comment'] ?? '';

    if ($comment !== '') {
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, name, email, content) VALUES (:post_id, :name, :email, :content)");
        $stmt->execute([
            'post_id' => $post_id,
            'name' => $name,
            'email' => $email,
            'content' => $comment
        ]);
    }
    header("Location: " . BASE_URL . "/posts/post.php?id=" . $post_id);
    exit;
}

// Get post
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id");
$stmt->execute(['id' => $post_id]);
$post = $stmt->fetch();

if (!$post) {
    echo "<p>Post not found.</p>";
    exit;
}

// Get comments
$stmt = $pdo->prepare("SELECT * FROM comments WHERE post_id = :post_id ORDER BY created_at DESC");
$stmt->execute(['post_id' => $post_id]);
$comments = $stmt->fetchAll();
?>

<div class="container">
    <h2><?= htmlspecialchars($post['title']) ?></h2>
    <small>Published on <?= $post['created_at'] ?></small>
    <?php if (!empty($post['image_url'])): ?>
        <div><img src="<?= htmlspecialchars($post['image_url']) ?>" alt="Post Image" style="max-width: 100%; height: auto;"></div>
    <?php endif; ?>
    <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>

    <hr>
    <h3>Leave a Comment</h3>
    <form method="POST">
        <input type="text" name="name" placeholder="Your name (optional)"><br><br>
        <input type="email" name="email" placeholder="Email (optional)"><br><br>
        <textarea name="comment" placeholder="Your comment..." required></textarea><br><br>
        <button type="submit">Post Comment</button>
    </form>

    <h3>Comments:</h3>
    <?php foreach ($comments as $comment): ?>
        <div style="margin-bottom: 15px;">
            <strong><?= htmlspecialchars($comment['name']) ?: 'Anonymous' ?></strong>
            <small><?= $comment['created_at'] ?></small>
            <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
