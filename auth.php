<?php
session_start();

// If the user is already logged in, redirect them to the home page immediately
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Determine if we should show the Login or Register form
$action = isset($_GET['action']) ? $_GET['action'] : 'login';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkillSwap | <?php echo ucfirst($action); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">

    <div class="auth-container">
        <a href="index.php" class="back-home">← Back to SkillSwap</a>

        <div class="auth-card">
            <div class="auth-tabs">
                <a href="auth.php?action=login" class="tab <?php echo $action == 'login' ? 'active' : ''; ?>">Login</a>
                <a href="auth.php?action=register" class="tab <?php echo $action == 'register' ? 'active' : ''; ?>">Register</a>
            </div>

            <?php if ($action == 'register'): ?>
                <form action="backend_logic.php?task=register" method="POST" class="fade-in">
                    <div class="input-group">
                        <label>Full Name</label>
                        <input type="text" name="name" placeholder="Enter your full name" required>
                    </div>
                    
                    <div class="input-group">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="example@email.com" required>
                    </div>

                    <div class="input-group">
                        <label>Age</label>
                        <input type="number" name="age" placeholder="How old are you?" required min="1" max="120">
                    </div>

                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Create a strong password" required>
                    </div>

                    <button type="submit" class="btn-main">Create Account</button>
                </form>

            <?php else: ?>
                <form action="backend_logic.php?task=login" method="POST" class="fade-in">
                    <div class="input-group">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="Enter your registered email" required>
                    </div>

                    <div class="input-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Enter your password" required>
                    </div>

                    <button type="submit" class="btn-main">Login to Account</button>
                    
                    <div class="toggle-text" style="margin-top: 15px; font-size: 13px; text-align: center;">
                        <a href="#" style="color: #888; text-decoration: none;">Forgot password?</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>