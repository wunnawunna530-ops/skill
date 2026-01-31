<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us | SkillSwap</title>
    <link rel="stylesheet" href="style.css">
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
</style>                    <a href="index.php" class="nav-btn btn-primary" style="color:white;">← Back</a>
        </div>
    </nav>
    <div class="profile-page-wrapper">
        <div class="profile-card" style="text-align: left; max-width: 500px;">
            <h1>Contact Us</h1>
            <p style="color: #777; margin-bottom: 25px;">Have a question about the SkillSwap community?</p>
            
            <form action="send_contact.php" method="POST">
                <label>Your Email</label>
                <input type="email" name="email" required style="width:100%; padding:12px; background:#222; border:1px solid #333; color:white; margin-bottom:15px;">
                
                <label>Message</label>
                <textarea name="message" rows="5" required style="width:100%; padding:12px; background:#222; border:1px solid #333; color:white; margin-bottom:20px;"></textarea>
                
                <button type="submit" class="nav-btn btn-primary" style="width:100%; color:white;">Send Message</button>
            </form>
        </div>
    </div>
</body>
</html>