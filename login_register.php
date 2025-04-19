<?php

session_start();
require_once 'config.php';

// Handle registration
if (isset($_POST['register'])) { // Check if the register form is submitted

    // Retrieve and sanitize form inputs
    $name = trim($_POST['registerName']);
    $email = trim($_POST['registerEmail']);
    $password = trim($_POST['registerPassword']);
    $role = trim($_POST['registerRole']);

    // Validate input fields
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        $_SESSION['register_error'] = 'All fields are required.';
        $_SESSION['active_form'] = 'register';
        header('Location: index.php');
        exit();
    }

    // Check if email already exists in the database
    $checkEmail = $conn->query("SELECT email FROM user WHERE email = '$email'");
    if ($checkEmail->num_rows > 0) {
        $_SESSION['register_error'] = 'Email already exists.';
        $_SESSION['active_form'] = 'register';
    } else {
        // Hash the password and insert the user into the database
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO user (name, email, password, role) VALUES (?, ?, ?, ?)");
        if ($stmt === false) {
            $_SESSION['register_error'] = 'Database error: Unable to prepare statement.';
            header('Location: index.php');
            exit();
        }

        // Bind parameters
        if (!$stmt->bind_param("ssss", $name, $email, $hashedPassword, $role)) {
            $_SESSION['register_error'] = 'Database error: Unable to bind parameters.';
            header('Location: index.php');
            exit();
        }

        // Execute the statement
        if ($stmt->execute()) {
            $_SESSION['register_success'] = 'Registration successful. You can now log in.';
        } else {
            $_SESSION['register_error'] = 'Database error: Unable to execute statement.';
        }

        // Close the statement
        $stmt->close();
    }

    header('Location: index.php');
    exit();
}

// Handle login
if (isset($_POST['login'])) { // Check if the login form is submitted
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate input fields
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = 'Both email and password are required.';
        $_SESSION['active_form'] = 'login';
        header('Location: index.php');
        exit();
    }

    // Check if the user exists in the database
    $result = $conn->query("SELECT * FROM user WHERE email = '$email'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] == 'admin') {
                header('Location: admin_dashboard.php');
            } else {
                header('Location: user_dashboard.php');
            }
            exit();
        } else {
            $_SESSION['login_error'] = 'Invalid email or password.';
        }
    } else {
        $_SESSION['login_error'] = 'Invalid email or password.';
    }

    $_SESSION['active_form'] = 'login';
    header('Location: index.php');
    exit();
}
?>