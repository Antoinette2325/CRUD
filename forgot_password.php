<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include Composer's autoloader

if (isset($_POST['forgotPassword'])) {
    $email = $_POST['forgotEmail'];

    // Check if the email exists in the database
    // Replace this with your database connection and query
    $conn = new mysqli('localhost', 'root', '', 'users_db');
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generate a unique token
        $token = bin2hex(random_bytes(50));

        // Save the token to the database
        $stmt = $conn->prepare("UPDATE user SET reset_token = ?, token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();

        // Send the reset email
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Correct SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = ''; // Your Gmail address
            $mail->Password = ''; // Use an App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use TLS encryption
            $mail->Port = 587; // Use 587 for TLS

            // Recipients
            $mail->setFrom('', 'Your App Name');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Click the link below to reset your password:<br>
                <a href='http://CRUD/reset_password.php?token=$token'>Reset Password</a>";

            $mail->send();
            echo 'A password reset link has been sent to your email.';
        } catch (Exception $e) {
            echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo 'No account found with that email.';
    }
}
?>