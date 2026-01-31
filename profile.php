<?php
session_start();
include 'db.php';

// 1. AUTHENTICATION
if (!isset($_SESSION['user'])) {
    header("Location: auth.php");
    exit();
}

$user = $_SESSION['user']; 
$user_id = intval($user['id']);

// 2. HANDLE UNFOLLOW ACTION
if (isset($_POST['unfollow_id'])) {
    $unfollow_id = intval($_POST['unfollow_id']);
    $stmt = $conn->prepare("DELETE FROM follows WHERE follower_id = ? AND followed_id = ?");
    $stmt->bind_param("ii", $user_id, $unfollow_id);
    $stmt->execute();
    header("Location: profile.php"); // Refresh to update list
    exit();
}

// 3. FETCH VIDEO COUNT
$query = "SELECT COUNT(*) as total FROM posts WHERE user_id = '$user_id'";
$result = $conn->query($query);
$post_data = $result->fetch_assoc();

// 4. FETCH FOLLOWING LIST (Joining with users table to get their names)
$following_query = "SELECT users.id, users.name, users.email 
                    FROM follows 
                    JOIN users ON follows.followed_id = users.id 
                    WHERE follows.follower_id = '$user_id'";
$following_result = $conn->query($following_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | SkillSwap</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .following-section {
            margin-top: 30px;
            text-align: left;
            border-top: 1px solid #333;
            padding-top: 20px;
        }
        .following-list {
            display: grid;
            gap: 12px;
            margin-top: 15px;
        }
        .follower-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(255,255,255,0.05);
            padding: 10px 15px;
            border-radius: 8px;
        }
        .follower-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .follower-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
        }
        .unfollow-btn {
            background: transparent;
            border: 1px solid #ff4b5c;
            color: #ff4b5c;
            padding: 4px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: 0.3s;
        }
        .unfollow-btn:hover {
            background: #ff4b5c;
            color: white;
        }
    </style>
</head>
<body class="dark-theme">
    <div class="profile-page-wrapper">
        <div class="profile-card">
            <a href="index.php" style="color: var(--primary); text-decoration: none; display: block; margin-bottom: 20px; text-align: left;">← Back to Home</a>
            
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" class="profile-avatar-large">
            
            <h1><?php echo htmlspecialchars($user['name']); ?></h1>
            <p style="color: var(--text-gray); font-size: 0.9rem; margin-bottom: 20px;"><?php echo htmlspecialchars($user['email']); ?></p>

            <div class="stats-row">
                <div class="stat">
                    <label>Age</label>
                    <span><?php echo htmlspecialchars($user['age']); ?> Years</span>
                </div>
                <div class="stat">
                    <label>Shared</label>
                    <span><?php echo $post_data['total']; ?> Videos</span>
                </div>
            </div>

            <div class="following-section">
                <h3 style="color: #fff; margin-bottom: 10px;">Users You Follow</h3>
                <div class="following-list">
                    <?php if ($following_result && $following_result->num_rows > 0): ?>
                        <?php while($f_row = $following_result->fetch_assoc()): ?>
                            <div class="follower-card">
                                <div class="follower-info">
                                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" class="follower-avatar">
                                    <div>
                                        <div style="color: #fff; font-size: 0.9rem;"><?php echo htmlspecialchars($f_row['name']); ?></div>
                                    </div>
                                </div>
                                <form method="POST" style="margin: 0;">
                                    <input type="hidden" name="unfollow_id" value="<?php echo $f_row['id']; ?>">
                                    <button type="submit" class="unfollow-btn">Unfollow</button>
                                </form>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="color: #666; font-size: 0.85rem; text-align: center;">You are not following anyone yet.</p>
                    <?php endif; ?>
                </div>
            </div>

            <a href="logout.php" class="logout-link-profile" style="margin-top: 30px; display: inline-block;">Log Out of Account</a>
        </div>
    </div>
</body>
</html>