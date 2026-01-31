<?php
session_start();
include 'db.php'; 

$is_logged_in = isset($_SESSION['user']);
$user_id = $is_logged_in ? intval($_SESSION['user']['id']) : 0;

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category_filter = isset($_GET['cat']) ? mysqli_real_escape_string($conn, $_GET['cat']) : 'all';

$trending_posts = [];
$categories = ['sport', 'technical', 'languages', 'games'];

foreach ($categories as $cat) {
    $t_query = "SELECT posts.*, users.name AS uploader_name 
                FROM posts 
                JOIN users ON posts.user_id = users.id 
                WHERE category = '$cat'";
    
    // Age restriction check
    if (!$is_logged_in || ($is_logged_in && $_SESSION['user']['age'] <= 10)) {
        $t_query .= " AND age_limit = 'under_10'";
    }
    
    $t_query .= " ORDER BY views DESC LIMIT 1";
    $t_res = $conn->query($t_query);
    if ($t_res && $t_res->num_rows > 0) {
        $trending_posts[] = $t_res->fetch_assoc();
    }
}

// Fallback: If no trending found per category, just get top 4 viewed videos total
if (empty($trending_posts)) {
    $fallback_query = "SELECT posts.*, users.name AS uploader_name 
                       FROM posts 
                       JOIN users ON posts.user_id = users.id 
                       ORDER BY views DESC LIMIT 4";
    $f_res = $conn->query($fallback_query);
    if ($f_res) {
        while($row = $f_res->fetch_assoc()) { 
            $trending_posts[] = $row; 
        }
    }
}

// --- 2. FEED LOGIC: Newest first ---
$query = "SELECT posts.*, users.name AS uploader_name 
          FROM posts 
          JOIN users ON posts.user_id = users.id 
          WHERE 1=1";

if (!$is_logged_in || ($is_logged_in && $_SESSION['user']['age'] <= 10)) {
    $query .= " AND age_limit = 'under_10'";
}
if ($category_filter !== 'all' && $category_filter !== '') { 
    $query .= " AND category = '$category_filter'"; 
}
if ($search !== '') { 
    $query .= " AND (title LIKE '%$search%' OR description LIKE '%$search%')"; 
}

$query .= " ORDER BY posts.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SkillSwap | Feed</title>
    <link rel="stylesheet" href="style.css">
    <style>
        a { text-decoration: none !important; color: inherit !important; }
        .section-title { margin: 40px 0 20px; color: var(--primary); font-size: 1.5rem; border-left: 4px solid var(--primary); padding-left: 15px; }
        
        /* View Counter Styling */
        .views-count {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.8rem;
            color: #888;
            margin-top: 5px;
        }

        .trending-card { border: 2px solid var(--primary); }
        .thumb-video { width: 100%; height: 100%; object-fit: cover; display: block; }
        .post-date { font-size: 0.75rem; color: #777; }
        .follow-badge { font-size: 0.7rem; background: #28a745; color: white; padding: 2px 6px; border-radius: 4px; margin-left: 5px; }
    </style>
</head>
<body class="dark-theme">

<nav class="navbar">
    <div class="nav-container">
<a href="index.php" style="
    text-decoration: none; 
    font-family: 'Inter', sans-serif;
    font-weight: 800;
    font-size: 2rem;
    display: flex;
    cursor: pointer;
    user-select: none;
">
    <div class="wave-text" style="display: flex;">
        <span style="color: #ffffff; display: inline-block; animation: wave 2s infinite ease-in-out; animation-delay: 0.1s;">S</span>
        <span style="color: #ffffff; display: inline-block; animation: wave 2s infinite ease-in-out; animation-delay: 0.2s;">k</span>
        <span style="color: #ffffff; display: inline-block; animation: wave 2s infinite ease-in-out; animation-delay: 0.3s;">i</span>
        <span style="color: #ffffff; display: inline-block; animation: wave 2s infinite ease-in-out; animation-delay: 0.4s;">l</span>
        <span style="color: #ffffff; display: inline-block; animation: wave 2s infinite ease-in-out; animation-delay: 0.5s;">l</span>
        
        <span style="width: 8px;"></span>

        <span style="color: #ffffff; display: inline-block; animation: wave 2s infinite ease-in-out; animation-delay: 0.6s;">S</span>
        <span style="color: #ffffff; display: inline-block; animation: wave 2s infinite ease-in-out; animation-delay: 0.7s;">w</span>
        <span style="color: #ffffff; display: inline-block; animation: wave 2s infinite ease-in-out; animation-delay: 0.8s;">a</span>
        <span style="color: #ffffff; display: inline-block; animation: wave 2s infinite ease-in-out; animation-delay: 0.9s;">p</span>
    </div>
</a>

<style>
@keyframes wave {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); color: #d0052a; text-shadow: 0 5px 15px rgba(255, 0, 47, 0.4); }
}
/* Optional: Only the Skill parts stay white at the peak if you prefer, 
   but this color shift makes it look more "alive" */
</style>        
        <div class="nav-wrapper" style="display: flex; gap: 15px; align-items: center;">
    <?php 
    $category_filter = isset($_GET['cat']) ? $_GET['cat'] : ''; 
    $categories = ['sport' => 'Sport', 'technical' => 'Technical', 'languages' => 'Languages', 'games' => 'Games'];

    foreach ($categories as $key => $name): 
        // Check if this is the current active category
        $isActive = ($category_filter == $key);
    ?>
        <div class="nav-item">
            <a href="index.php?cat=<?php echo $key; ?>" 
               style="
                text-decoration: none;
                padding: 8px 16px;
                border-radius: 8px;
                font-weight: 600;
                transition: all 0.2s ease;
                display: inline-block;
                /* Active state logic */
                color: <?php echo $isActive ? '#ff002f' : '#ffffff'; ?>;
                background: <?php echo $isActive ? 'rgba(255, 0, 47, 0.1)' : 'transparent'; ?>;
                border: 1px solid <?php echo $isActive ? '#ff002f' : 'transparent'; ?>;
               "
               class="nav-click-effect"
            >
                <?php echo $name; ?>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<style>
    /* The Click Button Effect */
    .nav-click-effect:active {
        transform: scale(0.92); /* Shrinks slightly when clicked */
        filter: brightness(1.2);
    }
    
    .nav-click-effect:hover {
        color: #ff002f !important;
        background: rgba(255, 0, 47, 0.05) !important;
    }
</style>

       <form action="index.php" method="GET" class="search-form">
    <div class="search-wrapper">
        <input type="text" name="search" placeholder="Search skills..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">🔍</button>
    </div>
    
    <?php if($category_filter !== 'all' && $category_filter !== ''): ?>
        <input type="hidden" name="cat" value="<?php echo htmlspecialchars($category_filter); ?>">
    <?php endif; ?>
</form>
            <?php if($category_filter !== 'all'): ?>
                <input type="hidden" name="cat" value="<?php echo $category_filter; ?>">
            <?php endif; ?>
        </form>

        <div class="user-area">
            <?php if ($is_logged_in): ?>
                <a href="upload.php" class="upload-trigger">+ Upload (2-5min)</a>

                <a href="profile.php" class="profile-dropdown">
                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" class="avatar">
                    <span class="user-name"><?php echo $_SESSION['user']['name']; ?></span>
                </a>
            <a href="logout.php" class="logout-link">Logout</a>
        <?php else: ?>
            <a href="auth.php" class="upload-trigger">Sign In</a>
        <?php endif; ?>
</div>
    </div>
</nav>
<div class="main-container">
    
    <?php if(($category_filter == 'all' || $category_filter == '') && $search == ''): ?>
    <h2 class="section-title">🔥 Trending (Most Viewed)</h2>
    <div class="video-grid">
        <?php foreach($trending_posts as $row): ?>
            <div class="card trending-card">
                <a href="watch.php?id=<?php echo $row['id']; ?>">
                    <div class="media-box">
                        <video class="thumb-video" muted>
                            <source src="<?php echo $row['video_path']; ?>#t=0.5">
                        </video>
                    </div>
                    <div class="card-details">
                        <span class="badge"><?php echo strtoupper($row['category']); ?></span>
                        <h3 style="color: #fff; margin: 5px 0;"><?php echo htmlspecialchars($row['title']); ?></h3>
                        <span style="color: var(--primary);">by <?php echo htmlspecialchars($row['uploader_name']); ?></span>
                        <div class="views-count">👁️ <?php echo number_format($row['views']); ?> views</div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

    <h2 class="section-title">🕒 Latest Skills</h2>
    <main class="video-grid">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): 
                $uploader_id = intval($row['user_id']);
                $is_following = false;
                if($is_logged_in && $user_id !== $uploader_id) {
                    $check = $conn->query("SELECT id FROM follows WHERE follower_id = $user_id AND followed_id = $uploader_id");
                    if($check && $check->num_rows > 0) $is_following = true;
                }
            ?>
                <div class="card">
                    <a href="watch.php?id=<?php echo $row['id']; ?>">
                        <div class="media-box">
                            <video class="thumb-video" muted><source src="<?php echo $row['video_path']; ?>#t=0.5"></video>
                        </div>
                        <div class="card-details">
                            <span class="post-date"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></span>
                            <h3 style="color: #fff; margin: 5px 0;"><?php echo htmlspecialchars($row['title']); ?></h3>
                            <span style="color: #ff4b5c;">
                                 <?php echo htmlspecialchars($row['uploader_name']); ?>
                                <?php if($is_following): ?><span class="follow-badge">Followed</span><?php endif; ?>
                            </span>
                            <div class="views-count">👁️ <?php echo number_format($row['views']); ?> views</div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color: white; text-align: center; width: 100%;">No skills found.</p>
        <?php endif; ?>
    </main>
</div>

<footer class="main-footer">
    <div class="footer-grid">
        <div class="footer-about">
<a href="index.php" style="
    text-decoration: none; 
    font-family: 'Inter', sans-serif;
    font-weight: 800;
    font-size: 2rem;
    display: flex;
    cursor: pointer;
    user-select: none;
">
    <div class="wave-text" style="display: flex;">
        <span style="color: #ffffff; display: inline-block; animation: wave 2s infinite ease-in-out; animation-delay: 0.1s;">S</span>
        <span style="color: #ffffff; display: inline-block; animation: wave 2s infinite ease-in-out; animation-delay: 0.2s;">k</span>
        <span style="color: #ffffff; display: inline-block; animation: wave 2s infinite ease-in-out; animation-delay: 0.3s;">i</span>
        <span style="color: #ffffff; display: inline-block; animation: wave 2s infinite ease-in-out; animation-delay: 0.4s;">l</span>
        <span style="color: #ffffff; display: inline-block; animation: wave 2s infinite ease-in-out; animation-delay: 0.5s;">l</span>
        
        <span style="width: 8px;"></span>

        <span style="color: #ffffff; display: inline-block; animation: wave 2s infinite ease-in-out; animation-delay: 0.6s;">S</span>
        <span style="color: #ffffff; display: inline-block; animation: wave 2s infinite ease-in-out; animation-delay: 0.7s;">w</span>
        <span style="color: #ffffff; display: inline-block; animation: wave 2s infinite ease-in-out; animation-delay: 0.8s;">a</span>
        <span style="color: #ffffff; display: inline-block; animation: wave 2s infinite ease-in-out; animation-delay: 0.9s;">p</span>
    </div>
</a>

<style>
@keyframes wave {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); color: #d0052a; text-shadow: 0 5px 15px rgba(255, 0, 47, 0.4); }
}
/* Optional: Only the Skill parts stay white at the peak if you prefer, 
   but this color shift makes it look more "alive" */
</style>        
<p style="color: #aaaaaa; font-size: 0.95rem; line-height: 1.6; margin-bottom: 25px; max-width: 320px;">
        Empowering creative minds to master new skills through community-driven knowledge sharing. Join our global mission to unlock your full potential.
    </p>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
                <div class="socials">
               <div style="display: flex; gap: 15px;">
        <a href="#" style="
            width: 40px; height: 40px; border-radius: 50%; 
            background: rgba(255, 255, 255, 0.05); color: #ffffff; 
            display: flex; align-items: center; justify-content: center; 
            text-decoration: none; border: 1px solid #333333; 
            transition: all 0.3s ease;"
            onmouseover="this.style.background='#1877F2'; this.style.borderColor='#1877F2'; this.style.transform='translateY(-5px)'"
            onmouseout="this.style.background='rgba(255, 255, 255, 0.05)'; this.style.borderColor='#333333'; this.style.transform='translateY(0)'">
            <i class="fab fa-facebook-f"></i>
        </a>

        <a href="#" style="
            width: 40px; height: 40px; border-radius: 50%; 
            background: rgba(255, 255, 255, 0.05); color: #ffffff; 
            display: flex; align-items: center; justify-content: center; 
            text-decoration: none; border: 1px solid #333333; 
            transition: all 0.3s ease;"
            onmouseover="this.style.background='linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%)'; this.style.borderColor='transparent'; this.style.transform='translateY(-5px)'"
            onmouseout="this.style.background='rgba(255, 255, 255, 0.05)'; this.style.borderColor='#333333'; this.style.transform='translateY(0)'">
            <i class="fab fa-instagram"></i>
        </a>

        <a href="#" style="
            width: 40px; height: 40px; border-radius: 50%; 
            background: rgba(255, 255, 255, 0.05); color: #ffffff; 
            display: flex; align-items: center; justify-content: center; 
            text-decoration: none; border: 1px solid #333333; 
            transition: all 0.3s ease;"
            onmouseover="this.style.background='#000000'; this.style.borderColor='#ff002f'; this.style.transform='translateY(-5px)'"
            onmouseout="this.style.background='rgba(255, 255, 255, 0.05)'; this.style.borderColor='#333333'; this.style.transform='translateY(0)'">
            <i class="fab fa-tiktok"></i>
        </a>
    </div>
            </div>
        </div>
        <div class="footer-links">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="mission.php">Our Mission</a></li>
                 <li><a href="contact.php">Contact Us</a></li>
            </ul>
        </div>
        <div class="footer-links">
            <h3>Support & Legal</h3>
            <ul>
                <li><a href="privacy.php">Privacy Policy</a></li>
                <li><a href="terms.php">Terms of Service</a></li>
                <li><a href="help.php">Help Center</a></li>
            </ul>
        </div>
        <div class="footer-newsletter" style="flex: 1; min-width: 250px;">
    <h3 style="color: #ff002f; margin-bottom: 10px; font-weight: 800;">Stay Updated</h3>
    <p style="color: #aaaaaa; margin-bottom: 20px; font-size: 0.95rem;">Get learning tips and updates.</p>
    
    <div style="
        position: relative;
        width: 100%;
        max-width: 350px;
        height: 52px;
        padding: 2px; /* Border Thickness */
        background: #1a1a1a;
        border-radius: 10px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    ">
        <div style="
            position: absolute;
            width: 180%;
            height: 180%;
            /* Only one white light spot defined in the gradient */
            background: conic-gradient(from 0deg, transparent 70%, #ffffff 90%, transparent 100%);
            animation: rotate-single-light 5s linear infinite;
        "></div>

        <div style="
            position: relative;
            z-index: 2;
            width: 100%;
            height: 100%;
            background: #0d0d0d;
            border-radius: 8px;
            display: flex;
            overflow: hidden;
        ">
            <input type="email" placeholder="Enter your email" style="
                flex: 1;
                background: transparent;
                border: none;
                color: white;
                padding: 10px 15px;
                outline: none;
                font-size: 0.9rem;
            ">
            <button style="
                background: #ff002f;
                border: none;
                color: white;
                padding: 0 25px;
                cursor: pointer;
                font-weight: bold;
                transition: background 0.3s ease;
            " 
            onmouseover="this.style.background='#cc0026'" 
            onmouseout="this.style.background='#ff002f'">
                Join
            </button>
        </div>
    </div>
</div>

<style>
/* Smooth rotation for the single light spot */
@keyframes rotate-single-light {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 SkillSwap Community. All Rights Reserved.</p>
    </div>
</footer>
<div style="position: fixed; bottom: 30px; right: 30px; z-index: 1000;">
    <a href="#" 
       style="
        display: flex;
        align-items: center;
        justify-content: center;
        width: 45px;
        height: 45px;
        background-color: #ff002f;
        color: white;
        text-decoration: none;
        border-radius: 50%;
        font-size: 20px;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(255, 0, 47, 0.4);
        transition: transform 0.2s ease;
        /* THE SHAKE ANIMATION */
        animation: shake-animation 2.5s infinite ease-in-out;
       "
       onmousedown="this.style.transform='scale(0.9)'"
       onmouseup="this.style.transform='scale(1)'"
       onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;"
    >
        ↑
    </a>
</div>

<style>
@keyframes shake-animation {
    0% { transform: translate(0, 0); }
    10% { transform: translate(-2px, -2px) rotate(-1deg); }
    20% { transform: translate(2px, 0px) rotate(1deg); }
    30% { transform: translate(-2px, 2px) rotate(0deg); }
    40% { transform: translate(2px, -2px) rotate(1deg); }
    50% { transform: translate(-2px, 0px) rotate(-1deg); }
    60% { transform: translate(0, 0); } /* Brief pause in shaking */
    100% { transform: translate(0, 0); }
}
</style>
</body>
</html>