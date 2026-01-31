<?php
session_start();
include 'db.php';

$video_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_logged_in = isset($_SESSION['user']);
$current_user_id = $is_logged_in ? intval($_SESSION['user']['id']) : 0;

// HANDLE FOLLOW REQUEST
if(isset($_POST['follow_action']) && $is_logged_in) {
    $target_id = intval($_POST['target_id']);
    if($current_user_id !== $target_id) {
        $conn->query("INSERT IGNORE INTO follows (follower_id, followed_id) VALUES ($current_user_id, $target_id)");
    }
    header("Location: watch.php?id=$video_id");
    exit();
}

$query = "SELECT posts.*, users.name AS uploader_name FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id = $video_id";
$result = $conn->query($query);
$video = $result ? $result->fetch_assoc() : null;

if (!$video) { header("Location: index.php"); exit(); }

$uploader_id = intval($video['user_id']);
$is_following = false;
if($is_logged_in) {
    $check = $conn->query("SELECT id FROM follows WHERE follower_id = $current_user_id AND followed_id = $uploader_id");
    if($check && $check->num_rows > 0) $is_following = true;
    $record_view = "INSERT IGNORE INTO video_views (user_id, video_id) VALUES ($current_user_id, $video_id)";
    $conn->query($record_view);

    // Only update the main post count if a new row was actually added to the views table
    if ($conn->affected_rows > 0) {
        $conn->query("UPDATE posts SET views = views + 1 WHERE id = $video_id");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($video['title']); ?> | SkillSwap</title>
    <link rel="stylesheet" href="style.css">
    <style>
        a { text-decoration: none !important; color: inherit; }
        .follow-btn { background: #ff4b5c; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .following-status { color: #28a745; font-weight: bold; }
    </style>
</head>
<body class="dark-theme">
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">Skill<span>Swap</span></a>
            <a href="index.php" class="nav-btn btn-primary">← Back to Skills</a>
        </div>
    </nav>

    <div class="watch-page-wrapper">
        <div class="watch-card">
            <div class="video-main-box">
                <video controls autoplay muted playsinline style="width: 100%; border-radius: 8px 8px 0 0;">
                    <source src="<?php echo htmlspecialchars($video['video_path']); ?>" type="video/mp4">
                </video>
            </div>
            <div class="watch-details">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <span class="badge"><?php echo strtoupper(htmlspecialchars($video['category'])); ?></span>
                        <h1 style="margin: 10px 0; color: #fff;"><?php echo htmlspecialchars($video['title']); ?></h1>
                        <p style="color: #777; font-size: 0.9rem;">Uploaded: <?php echo date('F j, Y, g:i a', strtotime($video['created_at'])); ?></p>
                    </div>
                    
                    <div class="follow-section">
                        <?php if($is_logged_in && $current_user_id !== $uploader_id): ?>
                            <?php if(!$is_following): ?>
                                <form method="POST">
                                    <input type="hidden" name="target_id" value="<?php echo $uploader_id; ?>">
                                    <button type="submit" name="follow_action" class="follow-btn">Follow <?php echo htmlspecialchars($video['uploader_name']); ?></button>
                                </form>
                            <?php else: ?>
                                <span class="following-status">✓ Followed by you</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <p class="description-text" style="color: var(--text-gray); margin-top: 20px;">
                    <?php echo nl2br(htmlspecialchars($video['description'])); ?>
                </p>
            </div>
        </div>
    </div>
</body>
</html>