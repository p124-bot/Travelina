<?php

session_start();



include("../includes/config.php"); 


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

  
    $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    
    
    $stmt->bind_param("s", $username);
    
    
    $stmt->execute();
    
    
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    
    if ($admin && password_verify($password, $admin['password'])) {
        
        
        
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        
        
        
        $stmt->close();
        $conn->close();
        header("Location: dashboard.php");
        exit();
        
    } else {
        
        $stmt->close();
        $conn->close();
        header("Location: login.php?error=Invalid credentials");
        exit();
    }
    
} else {
    header("Location: login.php");
    exit();
}
?>