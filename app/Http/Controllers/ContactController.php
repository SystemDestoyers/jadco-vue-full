<?php

namespace App\Http\Controllers;

use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendContactForm;
use Illuminate\Support\Facades\Cache;

class ContactController extends Controller
{
    protected $messageService;
    
    /**
     * Create a new controller instance.
     *
     * @param MessageService $messageService
     * @return void
     */
    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }
    
    public function submit(Request $request)
    {
        // Validate the form data
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);
        
        try {
            // Save message to database
            $message = $this->messageService->createMessage($validated);
            
            // Send email notification if configured
            $this->sendEmailNotification($message);
            
            // Log the successful message submission
            Log::info('Contact form submitted', [
                'id' => $message->id,
                'email' => $message->email,
                'name' => "{$message->first_name} {$message->last_name}"
            ]);
            
            return redirect()->back()->with('success', 'Thank you for your message! We will get back to you soon.');
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error submitting contact form', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['message' => 'Sorry, there was a problem submitting your message. Please try again later.']);
        }
    }
    
    /**
     * Handle direct email contact form
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function sendEmail(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'message' => 'required|string|max:255',
        ]);

        try {
            $adminEmail = config('mail.admin_email', env('MAIL_ADMIN_EMAIL', 'jad@jadco.co'));
            $mailDriver = config('mail.default');
            
            // Log mail configuration for debugging
            Log::info('Mail configuration', [
                'driver' => $mailDriver,
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'from_address' => config('mail.from.address'),
                'admin_email' => $adminEmail
            ]);
            
            // Save message to database if it came from the main contact form
            if ($request->has('saveToDatabase') || strpos($request->header('Referer'), 'contact') !== false) {
                // Convert name to first and last name for database
                $nameParts = explode(' ', $data['name'], 2);
                $firstName = $nameParts[0];
                $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
                
                // Prepare data for database
                $messageData = [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'message' => $data['message']
                ];
                
                // Save message to database
                $message = $this->messageService->createMessage($messageData);
                
                Log::info('Contact form message saved to database', [
                    'id' => $message->id,
                    'email' => $message->email
                ]);
            }
            
            // DIRECT PHPMAILER SENDING - bypassing the queue system entirely
            $mailer = new \App\Services\PHPMailerService();
            
            // Get templates and owner name from cache
            $templates = Cache::get('email_templates', [
                'subject' => 'New Message from {name}',
                'greeting' => 'Hello {owner}',
                'success_message' => 'Thank you for your message! We will get back to you soon.',
                'signature' => 'Regards, JADCO Team'
            ]);
            
            $ownerName = env('OWNERNAME', 'Jehad');
            
            // Process template placeholders
            $subject = str_replace('{name}', $data['name'], $templates['subject'] ?? 'New Message from {name}');
            $greeting = str_replace('{owner}', $ownerName, $templates['greeting'] ?? 'Hello {owner}');
            $signature = $templates['signature'] ?? 'Regards, JADCO Team';
            
            // Build HTML body similar to Laravel's MailMessage with the original design and Jadco theme
            $body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>'. config('app.name') .'</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style>
/* Base */
body, body *:not(html):not(style):not(br):not(tr):not(code) {
    box-sizing: border-box;
    font-family: "Poppins", "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
    position: relative;
}
body {
    -webkit-text-size-adjust: none;
    background-color: #ffffff;
    color: #4a5568;
    height: 100%;
    line-height: 1.4;
    margin: 0;
    padding: 0;
    width: 100% !important;
}
.wrapper {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 100%;
    background-color: #f7fafc;
    margin: 0;
    padding: 0;
    width: 100%;
}
.content {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 100%;
    margin: 0;
    padding: 0;
    width: 100%;
}
.header {
    padding: 25px 0;
    text-align: center;
    background-color: #ffffff;
    border-bottom: 1px solid #e8e5ef;
}
.header a {
    color: #e0285a;
    font-size: 19px;
    font-weight: bold;
    text-decoration: none;
}
.body {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 100%;
    background-color: #f7fafc;
    border-bottom: 1px solid #f7fafc;
    border-top: 1px solid #f7fafc;
    margin: 0;
    padding: 0;
    width: 100%;
}
.inner-body {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 570px;
    background-color: #ffffff;
    border-color: #e8e5ef;
    border-radius: 6px;
    border-width: 1px;
    box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015);
    margin: 0 auto;
    padding: 0;
    width: 570px;
    position: relative;
    overflow: hidden;
}
.content-cell {
    max-width: 100vw;
    padding: 32px;
    position: relative;
}
h2 {
    color: #4a5568;
    font-size: 18px;
    font-weight: 600;
    margin-top: 0;
    text-align: left;
}
p {
    font-size: 16px;
    line-height: 1.5em;
    margin-top: 0;
    text-align: left;
}
.button {
    -webkit-text-size-adjust: none;
    border-radius: 4px;
    color: #fff;
    display: inline-block;
    overflow: hidden;
    text-decoration: none;
    background-color: #e0285a;
    border-bottom: 8px solid #e0285a;
    border-left: 18px solid #e0285a;
    border-right: 18px solid #e0285a;
    border-top: 8px solid #e0285a;
}
.footer {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 570px;
    margin: 0 auto;
    padding: 0;
    text-align: center;
    width: 570px;
}
.footer p {
    color: #b0adc5;
    font-size: 12px;
    text-align: center;
}
.background-image {
    position: absolute;
    top: 0;
    right: 0;
    width: 200px;
    height: 100%;
    background-image: url("https://jadco.co/images/logo.png");
    background-repeat: no-repeat;
    background-position: right top;
    background-size: 150px;
    opacity: 0.05;
    z-index: 0;
}
</style>
</head>
<body>
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    <!-- Header -->
                    <tr>
                        <td class="header">
                            <a href="' . url('/') . '">JADCO</a>
                        </td>
                    </tr>
                    
                    <!-- Email Body -->
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0">
                            <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                                <!-- Body content -->
                                <tr>
                                    <td class="content-cell">
                                        <div class="background-image"></div>
                                        <h2>' . $greeting . '</h2>
                                        <p>You have received a new enquiry from the JADCO website contact form.</p>
                                        <p>Please find the details below:</p>
                                        <p><strong>Name:</strong> ' . $data['name'] . '</p>
                                        <p><strong>Email:</strong> ' . $data['email'] . '</p>
                                        <p><strong>Phone:</strong> ' . $data['phone'] . '</p>
                                        <p><strong>Message:</strong></p>
                                        <p>' . nl2br($data['message']) . '</p>
                                        
                                        <!-- Action Button -->
                                        <p style="text-align: center; margin-top: 30px;">
                                            <a href="' . url('/admin/messages') . '" class="button" target="_blank">View All Messages</a>
                                        </p>
                                        
                                        <p>Thank you for using our application!</p>
                                        <p style="margin-top: 30px;">' . $signature . '</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td>
                            <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td class="content-cell" align="center">
                                        <p>© ' . date('Y') . ' JADCO. All rights reserved.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
            
            // Set reply-to as the sender's email
            $replyTo = [
                $data['email'] => $data['name']
            ];
            
            // Plain text alternative
            $altBody = strip_tags(str_replace('<br>', "\n", $body));
            
            $sent = $mailer->send(
                $adminEmail,  // To
                $subject,     // Subject
                $body,        // HTML body
                $altBody,     // Text body
                [],           // No attachments
                $replyTo      // Reply-to 
            );
            
            if (!$sent) {
                throw new \Exception("Failed to send email via PHPMailer");
            }
            
            // Log the successful message submission
            Log::info('Direct contact form email sent', [
                'email' => $data['email'],
                'name' => $data['name'],
                'mail_driver' => 'phpmailer_direct'
            ]);
            
            if ($request->expectsJson()) {
                $templates = Cache::get('email_templates', []);
                $successMessage = $templates['success_message'] ?? 'Thank you for your message! We will get back to you soon.';
                
                return response()->json([
                    'success' => true,
                    'message' => $successMessage
                ]);
            }
            
            $templates = Cache::get('email_templates', []);
            $successMessage = $templates['success_message'] ?? 'Thank you for your message! We will get back to you soon.';
            
            return redirect()->back()->with('success', $successMessage);
        } catch (\Exception $e) {
            // Log detailed error information
            Log::error('Error sending contact form email', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'data' => $data,
                'mail_config' => [
                    'driver' => config('mail.default'),
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port')
                ]
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sorry, there was a problem sending your message. Please try again later.',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['message' => 'Sorry, there was a problem sending your message. Please try again later.']);
        }
    }
    
    /**
     * Send email notification about new message
     * 
     * @param \App\Models\Message $message
     * @return void
     */
    private function sendEmailNotification($message)
    {
        // Check if email notifications are enabled
        if (!config('mail.enabled', false)) {
            return;
        }
        
        try {
            // Get admin email address
            $adminEmail = config('mail.admin_email', env('MAIL_ADMIN_EMAIL', 'jad@jadco.co'));
            
            // Format data for notification
            $data = [
                'name' => $message->first_name . ' ' . $message->last_name,
                'email' => $message->email,
                'phone' => $message->phone,
                'message' => $message->message
            ];
            
            // Get templates from cache
            $templates = Cache::get('email_templates', [
                'subject' => 'New Message from {name}',
                'greeting' => 'Hello {owner}',
                'success_message' => 'Thank you for your message! We will get back to you soon.',
                'signature' => 'Regards, JADCO Team'
            ]);
            
            // Get owner name from environment
            $ownerName = env('OWNERNAME', 'Jehad');
            
            // Process template placeholders
            $subject = str_replace('{name}', $data['name'], $templates['subject']);
            $greeting = str_replace('{owner}', $ownerName, $templates['greeting']);
            $signature = $templates['signature'];
            
            // Create PHPMailer instance
            $mailer = new \App\Services\PHPMailerService();
            
            // Build email content
            $body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>'. config('app.name') .'</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style>
/* Base */
body, body *:not(html):not(style):not(br):not(tr):not(code) {
    box-sizing: border-box;
    font-family: "Poppins", "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
    position: relative;
}
body {
    -webkit-text-size-adjust: none;
    background-color: #ffffff;
    color: #4a5568;
    height: 100%;
    line-height: 1.4;
    margin: 0;
    padding: 0;
    width: 100% !important;
}
.wrapper {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 100%;
    background-color: #f7fafc;
    margin: 0;
    padding: 0;
    width: 100%;
}
.content {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 100%;
    margin: 0;
    padding: 0;
    width: 100%;
}
.header {
    padding: 25px 0;
    text-align: center;
    background-color: #ffffff;
    border-bottom: 1px solid #e8e5ef;
}
.header a {
    color: #e0285a;
    font-size: 19px;
    font-weight: bold;
    text-decoration: none;
}
.body {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 100%;
    background-color: #f7fafc;
    border-bottom: 1px solid #f7fafc;
    border-top: 1px solid #f7fafc;
    margin: 0;
    padding: 0;
    width: 100%;
}
.inner-body {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 570px;
    background-color: #ffffff;
    border-color: #e8e5ef;
    border-radius: 6px;
    border-width: 1px;
    box-shadow: 0 2px 0 rgba(0, 0, 150, 0.025), 2px 4px 0 rgba(0, 0, 150, 0.015);
    margin: 0 auto;
    padding: 0;
    width: 570px;
    position: relative;
    overflow: hidden;
}
.content-cell {
    max-width: 100vw;
    padding: 32px;
    position: relative;
}
h2 {
    color: #4a5568;
    font-size: 18px;
    font-weight: 600;
    margin-top: 0;
    text-align: left;
}
p {
    font-size: 16px;
    line-height: 1.5em;
    margin-top: 0;
    text-align: left;
}
.button {
    -webkit-text-size-adjust: none;
    border-radius: 4px;
    color: #fff;
    display: inline-block;
    overflow: hidden;
    text-decoration: none;
    background-color: #e0285a;
    border-bottom: 8px solid #e0285a;
    border-left: 18px solid #e0285a;
    border-right: 18px solid #e0285a;
    border-top: 8px solid #e0285a;
}
.footer {
    -premailer-cellpadding: 0;
    -premailer-cellspacing: 0;
    -premailer-width: 570px;
    margin: 0 auto;
    padding: 0;
    text-align: center;
    width: 570px;
}
.footer p {
    color: #b0adc5;
    font-size: 12px;
    text-align: center;
}
.background-image {
    position: absolute;
    top: 0;
    right: 0;
    width: 200px;
    height: 100%;
    background-image: url("https://jadco.co/images/logo.png");
    background-repeat: no-repeat;
    background-position: right top;
    background-size: 150px;
    opacity: 0.05;
    z-index: 0;
}
</style>
</head>
<body>
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    <!-- Header -->
                    <tr>
                        <td class="header">
                            <a href="' . url('/') . '">JADCO</a>
                        </td>
                    </tr>
                    
                    <!-- Email Body -->
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0">
                            <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                                <!-- Body content -->
                                <tr>
                                    <td class="content-cell">
                                        <div class="background-image"></div>
                                        <h2>' . $greeting . '</h2>
                                        <p>You have received a new enquiry from the JADCO website contact form.</p>
                                        <p>Please find the details below:</p>
                                        <p><strong>Name:</strong> ' . $data['name'] . '</p>
                                        <p><strong>Email:</strong> ' . $data['email'] . '</p>
                                        <p><strong>Phone:</strong> ' . $data['phone'] . '</p>
                                        <p><strong>Message:</strong></p>
                                        <p>' . nl2br($data['message']) . '</p>
                                        
                                        <!-- Action Button -->
                                        <p style="text-align: center; margin-top: 30px;">
                                            <a href="' . url('/admin/messages') . '" class="button" target="_blank">View All Messages</a>
                                        </p>
                                        
                                        <p>Thank you for using our application!</p>
                                        <p style="margin-top: 30px;">' . $signature . '</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td>
                            <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td class="content-cell" align="center">
                                        <p>© ' . date('Y') . ' JADCO. All rights reserved.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
            
            // Set reply-to as the sender's email
            $replyTo = [
                $data['email'] => $data['name']
            ];
            
            // Plain text alternative
            $altBody = strip_tags(str_replace('<br>', "\n", $body));
            
            // Send email
            $sent = $mailer->send(
                $adminEmail,
                $subject,
                $body,
                $altBody,
                [],
                $replyTo
            );
            
            if (!$sent) {
                throw new \Exception("Failed to send email via PHPMailer");
            }
            
            Log::info('Email notification sent for contact form', [
                'message_id' => $message->id,
                'to' => $adminEmail,
                'method' => 'direct_phpmailer'
            ]);
                
        } catch (\Exception $e) {
            // Just log the error but don't break the form submission
            Log::warning('Failed to send email notification for contact form', [
                'message_id' => $message->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle contact form submission from API (Vue frontend)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiSubmit(Request $request)
    {
        // Log the request to help with debugging
        Log::info('Contact form API submission received', [
            'request_type' => $request->header('Content-Type'),
            'is_ajax' => $request->ajax(),
            'is_json' => $request->isJson()
        ]);
        
        // Validate the form data
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string',
        ]);
        
        try {
            // Save message to database
            $message = $this->messageService->createMessage($validated);
            
            // Send email notification directly
            $this->sendEmailNotification($message);
            
            // Log the successful message submission
            Log::info('Contact form submitted via API', [
                'id' => $message->id,
                'email' => $message->email,
                'name' => "{$message->first_name} {$message->last_name}"
            ]);
            
            // Return JSON response
            return response()->json([
                'success' => true,
                'message' => 'Thank you for your message! We will get back to you soon.'
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error submitting contact form via API', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'data' => $validated
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Sorry, there was a problem submitting your message. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 