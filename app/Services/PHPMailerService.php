<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Illuminate\Support\Facades\Log;

class PHPMailerService
{
    protected $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        
        // Configure PHPMailer with settings from .env
        try {
            // Server settings
            // Force enable debug for troubleshooting
            $this->mailer->SMTPDebug = SMTP::DEBUG_CONNECTION;
            $this->mailer->Debugoutput = function($str, $level) {
                Log::debug("PHPMailer Debug: $str");
            };
            
            // Set mailer type based on configuration
            $mailerType = config('mail.mailer', 'smtp');
            
            if ($mailerType === 'smtp') {
                $this->mailer->isSMTP();
                $this->mailer->Host       = env('MAIL_HOST', 'sandbox.smtp.mailtrap.io');
                $this->mailer->SMTPAuth   = true;
                $this->mailer->Username   = env('MAIL_USERNAME', '4de385a66ab5dd');
                $this->mailer->Password   = env('MAIL_PASSWORD', 'eb5de99468a2b3');
                $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $this->mailer->Port       = (int)env('MAIL_PORT', 587);
                
                // Add SMTPOptions to handle potential SSL/TLS verification issues
                $this->mailer->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];
                
                // Log SMTP configuration for debugging
                Log::info("PHPMailer SMTP Configuration", [
                    'host' => $this->mailer->Host,
                    'port' => $this->mailer->Port,
                    'encryption' => $this->mailer->SMTPSecure,
                    'username' => $this->mailer->Username
                ]);
            } elseif ($mailerType === 'sendmail') {
                $this->mailer->isSendmail();
            } elseif ($mailerType === 'mailgun') {
                // For API-based services, we'll still use SMTP credentials if available
                $this->mailer->isSMTP();
                $this->mailer->Host       = config('services.mailgun.host', env('MAILGUN_HOST', 'smtp.mailgun.org'));
                $this->mailer->SMTPAuth   = true;
                $this->mailer->Username   = config('services.mailgun.username', env('MAILGUN_USERNAME', ''));
                $this->mailer->Password   = config('services.mailgun.password', env('MAILGUN_PASSWORD', ''));
                $this->mailer->SMTPSecure = config('services.mailgun.encryption', 'tls');
                $this->mailer->Port       = (int)config('services.mailgun.port', 587);
            }
            
            // Common settings for all mailer types
            $this->mailer->CharSet    = 'UTF-8';
            $this->mailer->Encoding   = 'base64';
            $this->mailer->Timeout    = 60;
            
            // Default sender from config
            $fromAddress = config('mail.from.address', env('MAIL_FROM_ADDRESS', 'hello@example.com'));
            $fromName = config('mail.from.name', env('MAIL_FROM_NAME', 'JADCO Support'));
            
            // Set default sender
            $this->mailer->setFrom($fromAddress, $fromName);
            
            // Set reply-to address if different from from address
            $replyToAddress = config('mail.reply_to.address', null);
            $replyToName = config('mail.reply_to.name', null);
            
            if ($replyToAddress && $replyToAddress !== $fromAddress) {
                $this->mailer->addReplyTo($replyToAddress, $replyToName ?? $fromName);
            }
            
            // Set admin BCC if configured (for keeping copies of all emails)
            $adminEmail = env('MAIL_ADMIN_EMAIL', null);
            if ($adminEmail && config('mail.admin_bcc', false)) {
                $this->mailer->addBCC($adminEmail, env('OWNERNAME', 'Admin'));
            }
        } catch (Exception $e) {
            Log::error("PHPMailer initialization error: {$e->getMessage()}");
        }
    }

    /**
     * Send an email using PHPMailer
     * 
     * @param string|array $to Recipient email address(es)
     * @param string $subject Email subject
     * @param string $body Email body (HTML)
     * @param string $altBody Plain text alternative body
     * @param array $attachments Optional array of attachments [path => name]
     * @param array $replyTo Optional reply-to address [email => name]
     * @param array $cc Optional CC recipients [email => name]
     * @param array $bcc Optional BCC recipients [email => name]
     * @return bool Whether the email was sent successfully
     */
    public function send($to, $subject, $body, $altBody = '', $attachments = [], $replyTo = [], $cc = [], $bcc = [])
    {
        try {
            // Reset all recipients and attachments
            $this->mailer->clearAllRecipients();
            $this->mailer->clearAttachments();
            
            // Add recipients
            if (is_array($to)) {
                foreach ($to as $email => $name) {
                    if (is_numeric($email)) {
                        $this->mailer->addAddress($name); // Just email address
                    } else {
                        $this->mailer->addAddress($email, $name); // Email and name
                    }
                }
            } else {
                $this->mailer->addAddress($to); // Single email address
            }
            
            // Add CC recipients
            foreach ($cc as $email => $name) {
                if (is_numeric($email)) {
                    $this->mailer->addCC($name);
                } else {
                    $this->mailer->addCC($email, $name);
                }
            }
            
            // Add BCC recipients
            foreach ($bcc as $email => $name) {
                if (is_numeric($email)) {
                    $this->mailer->addBCC($name);
                } else {
                    $this->mailer->addBCC($email, $name);
                }
            }
            
            // Add reply-to addresses
            foreach ($replyTo as $email => $name) {
                if (is_numeric($email)) {
                    $this->mailer->addReplyTo($name);
                } else {
                    $this->mailer->addReplyTo($email, $name);
                }
            }
            
            // Add attachments
            foreach ($attachments as $path => $name) {
                if (is_numeric($path)) {
                    $this->mailer->addAttachment($name);
                } else {
                    $this->mailer->addAttachment($path, $name);
                }
            }
            
            // Set email content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;
            $this->mailer->AltBody = $altBody ?: strip_tags($body);
            
            // Log email attempt
            Log::info("Attempting to send email", [
                'to' => $to,
                'subject' => $subject,
                'from' => $this->mailer->From
            ]);
            
            // Send the email
            $this->mailer->send();
            
            // Log success
            Log::info("Email sent successfully", [
                'to' => $to,
                'subject' => $subject
            ]);
            
            return true;
        } catch (Exception $e) {
            Log::error("PHPMailer error: {$e->getMessage()}", [
                'error' => $e->getMessage(),
                'to' => $to,
                'subject' => $subject,
                'debug_output' => $this->mailer->ErrorInfo
            ]);
            return false;
        }
    }
    
    /**
     * Get the underlying PHPMailer instance for direct manipulation
     * 
     * @return PHPMailer
     */
    public function getMailer()
    {
        return $this->mailer;
    }
}
