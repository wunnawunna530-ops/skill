<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['age'] <= 10) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SkillSwap | Share a Skill</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="upload-container">
        <form action="save_post.php" method="POST" enctype="multipart/form-data" id="uploadForm">
            <h2>Post a New Skill</h2>
            
            <div class="input-group">
                <label>Video Title</label>
                <input type="text" name="title" placeholder="e.g. How to kick a curveball" required>
            </div>

            <div class="input-group">
                <label>Description</label>
                <textarea name="description" rows="4" placeholder="Describe what you are teaching..." required></textarea>
            </div>

            <div class="input-group">
                <label>Category</label>
                <select name="category" required>
                    <option value="sport">Sport</option>
                    <option value="technical">Technical</option>
                    <option value="languages">Languages</option>
                    <option value="games">Games</option>
                </select>
            </div>

            <div class="input-group">
                <label>Who can see this?</label>
                <div class="radio-group">
                    <label><input type="radio" name="age_limit" value="under_10" checked> Kids</label>
                    <label><input type="radio" name="age_limit" value="over_10"> Adults/Teens</label>
                </div>
            </div>

            <div class="input-group">
                <label>Upload Video (2-5 Minutes)</label>
                <input type="file" name="video" id="videoInput" accept="video/*" required>
                <p id="durationWarning" style="color: #ff4d6d; font-size: 12px; display: none; margin-top: 5px;">
                    ⚠️ Video must be between 2 and 5 minutes!
                </p>
            </div>

            <button type="submit" class="auth-btn" id="submitBtn">Publish Post</button>
            <a href="index.php" style="color: #888; display: block; margin-top: 15px; text-align: center; text-decoration: none;">Cancel</a>
        </form>
    </div>
    <script src="upload_check.js"></script>
</body>
</html>