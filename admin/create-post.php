<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $pinned = isset($_POST['pinned']) ? 1 : 0; // ✅ New
    $imageUrl = null;

    // Handle image upload and resize (fixed height = 600px)
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === 0) {
        $imageTmp = $_FILES['image']['tmp_name'];
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = __DIR__ . '/../assets/images/' . $imageName;

        list($width, $height, $type) = getimagesize($imageTmp);
        $fixedHeight = 600;
        $newWidth = intval(($fixedHeight / $height) * $width);

        switch ($type) {
            case IMAGETYPE_JPEG:
                $src = imagecreatefromjpeg($imageTmp);
                break;
            case IMAGETYPE_PNG:
                $src = imagecreatefrompng($imageTmp);
                break;
            case IMAGETYPE_GIF:
                $src = imagecreatefromgif($imageTmp);
                break;
            default:
                $src = null;
                break;
        }

        if ($src) {
            $dst = imagecreatetruecolor($newWidth, $fixedHeight);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $fixedHeight, $width, $height);
            imagejpeg($dst, $targetPath, 85);
            imagedestroy($src);
            imagedestroy($dst);
        } else {
            move_uploaded_file($imageTmp, $targetPath); // fallback
        }

        $imageUrl = '/blog-website/assets/images/' . $imageName;
    }

    // ✅ Insert post with pinned value
    $stmt = $pdo->prepare("INSERT INTO posts (title, content, image_url, pinned) VALUES (:title, :content, :image_url, :pinned)");
    $stmt->execute([
        'title' => $title,
        'content' => $content,
        'image_url' => $imageUrl,
        'pinned' => $pinned
    ]);

    $message = "✅ Post created successfully!";
}
?>

<?php include __DIR__ . '/../includes/header.php'; ?>
<link rel="stylesheet" href="../assets/css/style.css">

<div class="create-post-wrapper">
    <div class="post-card">
        <h2>Create a New Blog Post 📝</h2>

        <?php if ($message): ?>
            <div class="alert success"><?= $message ?></div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="dashboard.php" class="btn-dashboard">⬅️ Go to Dashboard</a>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Post Title</label>
                <input type="text" name="title" id="title" placeholder="Enter a catchy title..." required>
            </div>

            <div class="form-group">
                <label for="content">Content</label>
                <textarea name="content" id="content" rows="10" placeholder="Write your post content here..." required></textarea>
            </div>

            <div class="form-group">
                <label for="image">Feature Image</label>
                <input type="file" name="image" id="image" accept="image/*">
                <small>📐 Image will be resized to 600px height. Width will scale automatically.</small>
            </div>

            <!-- ✅ Pin checkbox -->
            <div class="form-group">
                <input type="checkbox" name="pinned" id="pinned">
                <label for="pinned">📌 Pin this post (Sponsored)</label>
            </div>

            <button type="submit">📤 Publish</button>
        </form>
    </div>
</div>
