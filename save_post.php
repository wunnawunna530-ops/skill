<?php
session_start();
include 'db.php';

// Safety check: Ensure user is logged in and over 10
if (!isset($_SESSION['user']) || $_SESSION['user']['age'] <= 10) {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Check if the POST data actually exists (prevents Undefined Index errors)
    if (empty($_POST) && empty($_FILES)) {
        die("Error: The file you tried to upload is too large for the server. Check php.ini limits.");
    }

    $title = mysqli_real_escape_string($conn, $_POST['title'] ?? 'Untitled');
    $desc = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
    $cat = $_POST['category'] ?? 'general';
    $age_limit = $_POST['age_limit'] ?? 'over_10';
    $user_id = $_SESSION['user']['id'];

    // 2. Handle File Upload
    // Check if the file was actually uploaded without errors
    if (!isset($_FILES['video']) || $_FILES['video']['error'] !== UPLOAD_ERR_OK) {
        die("Upload failed with error code: " . ($_FILES['video']['error'] ?? 'Missing File'));
    }

    $video_name = $_FILES['video']['name'];
    $video_tmp = $_FILES['video']['tmp_name'];
    $upload_dir = "uploads/";

    // 3. Create directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Create unique name to prevent overwriting
    $target_file = $upload_dir . time() . "_" . basename($video_name);

    if (move_uploaded_file($video_tmp, $target_file)) {
        // Use prepared statements if possible, but keeping your format for now:
        $sql = "INSERT INTO posts (title, description, category, age_limit, video_path, user_id) 
                VALUES ('$title', '$desc', '$cat', '$age_limit', '$target_file', '$user_id')";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: index.php?success=1");
            exit(); // Always exit after a header redirect
        } else {
            echo "Database Error: " . $conn->error;
        }
    } else {
        echo "Failed to move uploaded file to destination.";
    }
}
?>