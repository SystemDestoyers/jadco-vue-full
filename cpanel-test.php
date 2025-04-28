<?php
// cPanel SMTP Test Script

// Load the Composer autoloader
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

// Current working configuration from .env file
$smtp_host = 'sandbox.smtp.mailtrap.io'; // Current working SMTP server
$smtp_username = '4de385a66ab5dd'; // Current working username
$smtp_password = 'eb5de99468a2b3'; // Current working password
$smtp_port = 587; // Current working port
$smtp_encryption = 'tls'; // Current working encryption
$from_email = 'from@example.com'; // Current from address
$from_name = 'JadcoSupport'; // Current from name

// Who to send the test email to - using your admin email from .env
$recipient_email = 'jad@jadco.co'; // Your admin email
$ownerName = 'Jehad'; // Your owner name from .env

echo "=========================================\n";
echo "SMTP Test Script - Current Working Configuration\n";
echo "=========================================\n";
echo "Testing connection to: $smtp_host:$smtp_port\n";
echo "Using encryption: " . ($smtp_encryption ?: 'none') . "\n";
echo "Sending from: $from_email ($from_name)\n";
echo "Sending to: $recipient_email\n";
echo "=========================================\n\n";

try {
    // Enable verbose debug output
    $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Show server communication
    
    // Server settings
    $mail->isSMTP();
    $mail->Host = $smtp_host;
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_username;
    $mail->Password = $smtp_password;
    
    if ($smtp_encryption == 'tls') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    } elseif ($smtp_encryption == 'ssl') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    }
    
    $mail->Port = $smtp_port;
    
    // Set a longer timeout for slow servers
    $mail->Timeout = 60;
    
    // Recipients
    $mail->setFrom($from_email, $from_name);
    $mail->addAddress($recipient_email, $ownerName);
    $mail->addReplyTo($from_email, $from_name);
    
    // Content
    $mail->isHTML(true);
    $mail->Subject = 'SMTP Test with Current Config - ' . date('Y-m-d H:i:s');
    
    // HTML message
    $mail->Body = '
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e8e8e8; border-radius: 5px;">
        <h2 style="color: #444;">SMTP Test Successful!</h2>
        <p>This email confirms your SMTP settings are working correctly.</p>
        <p>Hello ' . $ownerName . ',</p>
        <p>This is a test email to verify the email sending functionality is working with your current configuration.</p>
        <p><strong>Details:</strong></p>
        <ul>
            <li>Server: ' . $smtp_host . '</li>
            <li>Port: ' . $smtp_port . '</li>
            <li>Username: ' . $smtp_username . '</li>
            <li>Encryption: ' . ($smtp_encryption ?: 'none') . '</li>
            <li>Sent: ' . date('Y-m-d H:i:s') . '</li>
        </ul>
        <p style="text-align: center; margin-top: 30px;">
            <a href="https://jadco.co" style="background-color: #e0285a; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-weight: bold;">Visit JADCO Website</a>
        </p>
        <p style="margin-top: 30px; font-size: 12px; color: #888;">
            This is an automated test email. Please do not reply.
        </p>
    </div>';
    
    // Plain text alternative
    $mail->AltBody = "SMTP Test Successful!\n\n" .
                    "This email confirms your SMTP settings are working correctly.\n\n" .
                    "Hello " . $ownerName . ",\n\n" .
                    "This is a test email to verify the email sending functionality is working with your current configuration.\n\n" .
                    "Details:\n" .
                    "- Server: " . $smtp_host . "\n" .
                    "- Port: " . $smtp_port . "\n" .
                    "- Username: " . $smtp_username . "\n" .
                    "- Encryption: " . ($smtp_encryption ?: 'none') . "\n" .
                    "- Sent: " . date('Y-m-d H:i:s') . "\n\n" .
                    "This is an automated test email. Please do not reply.";
    
    // Send the email
    echo "Attempting to send email...\n\n";
    $mail->send();
    
    echo "\n=========================================\n";
    echo "SUCCESS! Email sent successfully.\n";
    echo "=========================================\n";
    
} catch (Exception $e) {
    echo "\n=========================================\n";
    echo "ERROR: Email could not be sent.\n";
    echo "Error message: " . $mail->ErrorInfo . "\n";
    echo "=========================================\n";
    
    // Additional troubleshooting information
    echo "\nTroubleshooting Tips:\n";
    echo "1. Check if your password is correct\n";
    echo "2. Verify the SMTP host name is correct\n";
    echo "3. Make sure the port isn't blocked by your firewall\n";
    echo "4. Try different encryption settings (tls, ssl, or none)\n";
    echo "5. Check if your host requires specific authentication settings\n";
} 