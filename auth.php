<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'register') {
        // Handle registration
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if (empty($username) || empty($password)) {
            $_SESSION['error'] = "Username and password are required.";
            header("Location: register.php");
            exit;
        }

        // Check if username already exists
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = "Username already exists. Please choose a different username.";
            header("Location: register.php");
            exit;
        }

        // Hash the password and insert the new user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $stmt->execute([':username' => $username, ':password' => $hashed_password]);

        $_SESSION['success'] = "Registration successful! Please log in.";
        header("Location: login.php");
        exit;
    } else {
        // Handle login (existing logic)
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        // Authenticate against the users table
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            header("Location: index.php");
            exit;
        } else {
            $_SESSION['error'] = "Invalid username or password.";
            header("Location: login.php");
            exit;
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: login.php");
    exit;
}

header("Location: login.php");
exit;
?>
