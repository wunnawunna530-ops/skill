<?php
session_start(); // Access the current session
session_unset(); // Remove all session variables
session_destroy(); // Destroy the session entirely

// Send the user back to the homepage
header("Location: index.php");
exit();
?>