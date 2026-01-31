<?php
session_start();
include 'db.php'; // Connects to your skill_swap database

// Determine if the user is trying to register or login
$task = isset($_GET['task']) ? $_GET['task'] : '';

if ($task == 'register') {
    // 1. COLLECT DATA FROM REGISTER FORM
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $age = intval($_POST['age']);
    $password = $_POST['password'];

    // 2. ENCRYPT PASSWORD (Security best practice)
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // 3. INSERT INTO DATABASE
    $sql = "INSERT INTO users (name, email, age, password) VALUES ('$name', '$email', '$age', '$hashed_password')";
    
    if ($conn->query($sql) === TRUE) {
        // Get the ID of the user we just created
        $user_id = $conn->insert_id;

        // 4. CREATE SESSION (Log them in immediately)
        $_SESSION['user'] = [
            'id' => $user_id,
            'name' => $name,
            'email' => $email,
            'age' => $age
        ];

        // 5. REDIRECT TO HOME PAGE
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }

} elseif ($task == 'login') {
    // 1. COLLECT DATA FROM LOGIN FORM
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // 2. FIND USER IN DATABASE
    $result = $conn->query("SELECT * FROM users WHERE email='$email'");

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // 3. VERIFY PASSWORD
        if (password_verify($password, $row['password'])) {
            
            // 4. CREATE SESSION
            $_SESSION['user'] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'email' => $row['email'],
                'age' => $row['age']
            ];

            // 5. REDIRECT TO HOME PAGE
            header("Location: index.php");
            exit();
        } else {
            echo "<script>alert('Invalid Password'); window.location='auth.php?action=login';</script>";
        }
    } else {
        echo "<script>alert('User not found'); window.location='auth.php?action=register';</script>";
    }
} else {
    // If no task is defined, go back to home
    header("Location: index.php");
    exit();
}
?>