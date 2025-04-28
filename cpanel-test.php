<?php
// cPanel SMTP Test Script

// Load the Composer autoloader
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Create a new PHPMailer instance
$mail = new PHPMailer(true);

// Current cPanel configuration from .env file
$smtp_host = 'jadco.tech'; // Your cPanel mail server
$smtp_username = 'support-jadco@jadco.tech'; // Your cPanel email
$smtp_password = '0102030@lolo'; // Your cPanel email password
$smtp_port = 587; // cPanel port
$smtp_encryption = 'tls'; // cPanel encryption
$from_email = 'support-jadco@jadco.tech'; // From address
$from_name = 'JadcoSupport'; // From name

// Who to send the test email to - using an email on the same domain
$recipient_email = 'support-jadco@jadco.tech'; // Use the same email for testing
$ownerName = 'MR: Jehad'; // Your owner name from .env

echo "=========================================\n";
echo "cPanel SMTP Test Script\n";
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
    
    // Add SSL options to prevent verification issues on local/development
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];
    
    // Set a longer timeout for slow connections
    $mail->Timeout = 60;
    
    // Recipients
    $mail->setFrom($from_email, $from_name);
    $mail->addAddress($recipient_email, $ownerName);
    $mail->addReplyTo($from_email, $from_name);
    
    // Content
    $mail->isHTML(true);
    $mail->Subject = 'cPanel SMTP Test - ' . date('Y-m-d H:i:s');
    
    // HTML message
    $mail->Body = '
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e8e8e8; border-radius: 5px;">
        <h2 style="color: #444;">cPanel SMTP Test</h2>
        <p>This is a test email to verify your cPanel SMTP configuration.</p>
        <p>Hello ' . $ownerName . ',</p>
        <p>This is a test email from your localhost environment to verify the cPanel email settings are working correctly.</p>
        <p><strong>Connection Details:</strong></p>
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
    $mail->AltBody = "cPanel SMTP Test\n\n" .
                    "This is a test email to verify your cPanel SMTP configuration.\n\n" .
                    "Hello " . $ownerName . ",\n\n" .
                    "This is a test email from your localhost environment to verify the cPanel email settings are working correctly.\n\n" .
                    "Connection Details:\n" .
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
    
    // Additional troubleshooting information for cPanel specific issues
    echo "\nTroubleshooting cPanel SMTP Issues:\n";
    echo "1. Check if your cPanel password is correct\n";
    echo "2. Make sure the mail domain exists on your cPanel server\n";
    echo "3. Try using 'mail.jadco.tech' instead of 'jadco.tech' as the SMTP host\n";
    echo "4. Try port 465 with SSL encryption instead of port 587 with TLS\n";
    echo "5. Ensure outgoing port 587/465 isn't blocked by your firewall/local ISP\n";
    echo "6. Some ISPs block outgoing mail connections from residential connections\n";
    echo "7. Check if your cPanel has email sending restrictions for new accounts\n";
    echo "8. Localhost environments sometimes have issues with SSL certificates\n";
} 