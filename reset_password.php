<?php
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Connect to the database
    $conn = new mysqli('localhost', 'root', '', 'config.php');

    // Verify the token
    $stmt = $conn->prepare("SELECT * FROM user WHERE reset_token = ? AND token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Token is valid, show the reset password form
        if (isset($_POST['resetPassword'])) {
            $newPassword = $_POST['newPassword'];
            $confirmPassword = $_POST['confirmPassword'];

            if ($newPassword === $confirmPassword) {
                // Hash the new password
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

                // Update the password in the database
                $stmt = $conn->prepare("UPDATE user SET password = ?, reset_token = NULL, token_expiry = NULL WHERE reset_token = ?");
                $stmt->bind_param("ss", $hashedPassword, $token);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo 'Password has been reset successfully.';
                } else {
                    echo 'Failed to reset password.';
                }
            } else {
                echo 'Passwords do not match.';
            }
        }
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Reset Password</title>
        </head>
        <body>
            <form action="" method="post">
                <input type="password" name="newPassword" placeholder="Enter new password" required>
                <input type="password" name="confirmPassword" placeholder="Confirm new password" required>
                <button type="submit" name="resetPassword">Reset Password</button>
            </form>
        </body>
        </html>
        <?php
    } else {
        echo 'Invalid or expired token.';
    }
} else {
    echo 'No token provided.';
}
?>