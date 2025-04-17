<?php

session_start(); // Start the session

// Retrieve errors and active form from session
$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? '',
    'forgot' => $_SESSION['forgot_error'] ?? '',
];
$success = $_SESSION['register_success'] ?? $_SESSION['forgot_success'] ?? '';
$activeForm = $_SESSION['active_form'] ?? 'login'; // Default to login form

// Clear session variables after use
session_unset();

// Function to display error messages
function showError($error) {
    return !empty($error) ? "<div class='text-red-500 text-sm mb-4'>$error</div>" : '';
}

// Function to display success messages
function showSuccess($success) {
    return !empty($success) ? "<div class='text-green-500 text-sm mb-4'>$success</div>" : '';
}

// Function to determine active form
function isActiveForm($formName, $activeForm) {
    return $formName === $activeForm ? 'active' : 'hidden';
}
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll System - Login & Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-r from-orange-50 to-yellow-900 text-gray-800">
    <div class="container mx-auto px-4">
        <div class="form-box bg-white shadow-lg rounded-lg p-9 max-w-sm mx-auto">
            <div id="loginForm" class="<?php echo isActiveForm('login', $activeForm); ?>">
                <h1 class="text-3xl font-bold text-center mb-6">Payroll?</h1>
                <?php echo showError($errors['login']); ?>
                <form action="login_register.php" method="post">
                    <input type="email" id="email" name="email" placeholder="Email" required
                        class="w-full p-3 mb-4 bg-gray-100 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-950">
                    <input type="password" id="password" name="password" placeholder="Password" required
                        class="w-full p-3 mb-4 bg-gray-100 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-950">
                    <button type="submit" name="login"
                        class="w-full bg-yellow-900 text-white py-2 rounded-lg hover:bg-orange-950 transition duration-300">Login</button>
                    <p class="text-center mt-4 text-sm">Don't have an account? <a href="#" id="showRegister"
                            class="text-yellow-700 hover:text-orange-700 font-medium">Register</a></p>
                    <p class="text-center mt-4 text-sm">
                        <a href="#" id="forgotPassword" class="text-yellow-700 hover:text-orange-700 font-medium">Forgot Password?</a>
                    </p>
                </form>
            </div>
            <div id="registerForm" class="<?php echo isActiveForm('register', $activeForm); ?>">
                <h2 class="text-2xl font-bold text-center mb-9">Register</h2>
                <?php echo showError($errors['register']); ?>
                <?php echo showSuccess($success); ?>
                <form action="login_register.php" method="post">
                    <input type="text" id="registerName" name="registerName" placeholder="Name" required
                        class="w-full p-3 mb-4 bg-gray-100 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-950">
                    <input type="email" id="registerEmail" name="registerEmail" placeholder="Email" required
                        class="w-full p-3 mb-4 bg-gray-100 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-950">
                    <input type="password" id="registerPassword" name="registerPassword" placeholder="Password" required
                        class="w-full p-3 mb-4 bg-gray-100 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-950">
                    <select id="registerRole" name="registerRole" required
                        class="w-full p-3 mb-4 bg-gray-100 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-950">
                        <option value="" disabled selected>Select Role</option>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                    <button type="submit" name="register"
                        class="w-full bg-yellow-900 text-white py-2 rounded-lg hover:bg-orange-950 transition duration-300">Register</button>
                    <p class="text-center mt-4 text-sm">Already have an account? <a href="#" id="showLogin"
                            class="text-yellow-700 hover:text-orange-700 font-medium">Login</a></p>
                </form>
            </div>
            <div id="forgotPasswordForm" class="hidden">
                <h2 class="text-2xl font-bold text-center mb-9">Forgot Password</h2>
                <?php echo showError($errors['forgot']); ?>
                <?php echo showSuccess($success); ?>
                <form action="forgot_password.php" method="post">
                    <input type="email" id="forgotEmail" name="forgotEmail" placeholder="Enter your email" required
                        class="w-full p-3 mb-4 bg-gray-100 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-950">
                    <button type="submit" name="forgotPassword"
                        class="w-full bg-yellow-900 text-white py-2 rounded-lg hover:bg-orange-950 transition duration-300">Send Reset Link</button>
                    <p class="text-center mt-4 text-sm">
                        <a href="#" id="backToLogin" class="text-yellow-700 hover:text-orange-700 font-medium">Back to Login</a>
                    </p>
                </form>
            </div>
            <div id="resetPasswordForm" class="hidden">
                <h2 class="text-2xl font-bold text-center mb-9">Reset Password</h2>
                <form action="reset_password.php" method="post">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                    <input type="password" name="newPassword" placeholder="Enter your new password" required
                        class="w-full p-3 mb-4 bg-gray-100 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-950">
                    <button type="submit" class="w-full bg-yellow-900 text-white py-2 rounded-lg hover:bg-orange-950 transition duration-300">Reset Password</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const forgotPasswordForm = document.getElementById('forgotPasswordForm');
        const resetPasswordForm = document.getElementById('resetPasswordForm');
        const showRegister = document.getElementById('showRegister');
        const showLogin = document.getElementById('showLogin');
        const forgotPassword = document.getElementById('forgotPassword');
        const backToLogin = document.getElementById('backToLogin');

        showRegister.addEventListener('click', (e) => {
            e.preventDefault();
            loginForm.classList.add('hidden');
            registerForm.classList.remove('hidden');
            forgotPasswordForm.classList.add('hidden');
            resetPasswordForm.classList.add('hidden');
        });

        showLogin.addEventListener('click', (e) => {
            e.preventDefault();
            registerForm.classList.add('hidden');
            loginForm.classList.remove('hidden');
            forgotPasswordForm.classList.add('hidden');
            resetPasswordForm.classList.add('hidden');
        });

        forgotPassword.addEventListener('click', (e) => {
            e.preventDefault();
            loginForm.classList.add('hidden');
            forgotPasswordForm.classList.remove('hidden');
            resetPasswordForm.classList.add('hidden');
        });

        backToLogin.addEventListener('click', (e) => {
            e.preventDefault();
            forgotPasswordForm.classList.add('hidden');
            loginForm.classList.remove('hidden');
            resetPasswordForm.classList.add('hidden');
        });
    </script>
</body>
</html>