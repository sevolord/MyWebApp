<?php
    session_start();
    require_once 'config.php';

    function register_user($username, $email, $password) {
    global $pdo;

    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
    $stmt->execute(['username' => $username, 'email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        if ($user['username'] === $username) {
            return "Username already exists.";
        }
        if ($user['email'] === $email) {
            return "Email already exists.";
        }
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into the database
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
    $result = $stmt->execute([
        'username' => $username,
        'email' => $email,
        'password' => $hashed_password,
    ]);

    if ($result) {
        // Log the user in by setting session variables
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['username'] = $username;
        return true;
    }

    return "Registration failed. Please try again.";
    }

    function authenticate_user($username, $password) {
        global $pdo;
    
        // Retrieve user from the database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
    
        // Verify password
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
    
        return false;
    }
    
    function submit_ad($title, $description, $user_id) {
        global $pdo;
    
        // Insert ad into the database
        $stmt = $pdo->prepare("INSERT INTO ads (title, description, user_id) VALUES (:title, :description, :user_id)");
        $result = $stmt->execute([
            'title' => $title,
            'description' => $description,
            'user_id' => $user_id,
        ]);
    
        return $result;
    }
    
    function fetch_ads() {
        global $pdo;
    
        // Fetch ads from the database
        $stmt = $pdo->query("SELECT * FROM ads ORDER BY created_at DESC");
        $ads = $stmt->fetchAll();
    
        return $ads;
    }
    
    

?>
