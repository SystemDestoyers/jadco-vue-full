<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Cache;
use App\Services\PHPMailerService;

class SendContactForm extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $data;
    protected $useQueue;
    protected $templates;
    protected $usePHPMailer;

    public function __construct($data)
    {
        $this->data = $data;
        // Check if queue is enabled in settings (default to true if not set)
        $this->useQueue = Cache::get('use_email_queue', true);
        // Get email templates from settings
        $this->templates = Cache::get('email_templates', [
            'subject' => 'New Message from {name}',
            'greeting' => 'Hello {owner}',
            'success_message' => 'Thank you for your message! We will get back to you soon.',
            'signature' => 'Regards, JADCO Team'
        ]);
        // Use PHPMailer by default
        $this->usePHPMailer = Cache::get('use_phpmailer', true);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Always use database for queueing, we'll send with PHPMailer manually
        return ['database'];
    }

    /**
     * Determine if notification should be queued.
     *
     * @return bool
     */
    public function shouldQueue()
    {
        return $this->useQueue;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // The data for this method is used by the queue processor
        // But we'll still define it correctly for compatibility
        $ownerName = env('OWNERNAME', 'JEHAD');
        
        // Process template placeholders
        $subject = str_replace('{name}', $this->data['name'], $this->templates['subject'] ?? 'New Message from {name}');
        $greeting = str_replace('{owner}', $ownerName, $this->templates['greeting'] ?? 'Hello {owner}');
        $signature = $this->templates['signature'] ?? 'Regards, JADCO Team';
        
        // If direct sending is requested, use PHPMailer now
        if (!$this->useQueue) {
            $this->sendWithPHPMailer($notifiable, $subject, $greeting, $signature);
        }
        
        return (new MailMessage)
            ->theme('jadco')
            ->from(config('mail.from.address', env('MAIL_FROM_ADDRESS')), config('mail.from.name', env('MAIL_FROM_NAME')))
            ->subject($subject)
            ->greeting($greeting)
            ->line('You have received a new enquiry from the JADCO website contact form.')
            ->line('Please find the details below:')
            ->line('**Name:** ' . $this->data['name'])
            ->line('**Email:** ' . $this->data['email'])
            ->line('**Phone:** ' . $this->data['phone'])
            ->line('**Message:**')
            ->line($this->data['message'])
            ->action('View All Messages', url('/admin/messages'))
            ->line('Thank you for using our application!')
            ->salutation($signature);
    }

    /**
     * Send email using PHPMailer directly
     * 
     * @param object $notifiable
     * @param string $subject
     * @param string $greeting
     * @param string $signature
     * @return bool
     */
    protected function sendWithPHPMailer($notifiable, $subject, $greeting, $signature)
    {
        try {
            $mailer = new PHPMailerService();
            
            // Build HTML body similar to Laravel's MailMessage
            $body = '<div style="font-family: Arial, sans-serif; line-height: 1.6;">';
            $body .= '<h2 style="margin-bottom: 20px;">' . $greeting . '</h2>';
            $body .= '<p>You have received a new enquiry from the JADCO website contact form.</p>';
            $body .= '<p>Please find the details below:</p>';
            $body .= '<p><strong>Name:</strong> ' . $this->data['name'] . '</p>';
            $body .= '<p><strong>Email:</strong> ' . $this->data['email'] . '</p>';
            $body .= '<p><strong>Phone:</strong> ' . $this->data['phone'] . '</p>';
            $body .= '<p><strong>Message:</strong></p>';
            $body .= '<p>' . nl2br($this->data['message']) . '</p>';
            
            // Add action button
            $body .= '<div style="margin: 30px 0;">';
            $body .= '<a href="' . url('/admin/messages') . '" style="background-color: #3490dc; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-weight: bold;">View All Messages</a>';
            $body .= '</div>';
            
            $body .= '<p>Thank you for using our application!</p>';
            $body .= '<p style="margin-top: 30px;">' . $signature . '</p>';
            $body .= '</div>';
            
            // Get recipient email - prioritize configured admin email
            $to = $notifiable->email ?? 
                  $notifiable->mail ?? 
                  config('mail.admin_email', env('MAIL_ADMIN_EMAIL')) ?? 
                  env('OWNER_EMAIL');
            
            // Set reply-to as the sender's email
            $replyTo = [
                $this->data['email'] => $this->data['name']
            ];
            
            // Plain text alternative
            $altBody = strip_tags(str_replace('<br>', "\n", $body));
            
            // Log attempt
            \Illuminate\Support\Facades\Log::info("Sending contact form email", [
                'to' => $to,
                'subject' => $subject,
                'from' => config('mail.from.address', env('MAIL_FROM_ADDRESS', 'from@example.com')),
                'reply_to' => $this->data['email']
            ]);
            
            // Send the email
            return $mailer->send(
                $to,
                $subject,
                $body,
                $altBody,
                [], // No attachments
                $replyTo // Set reply-to as the contact form sender
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("PHPMailer error in notification: {$e->getMessage()}", [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'name' => $this->data['name'],
            'email' => $this->data['email'],
            'phone' => $this->data['phone'],
            'message' => $this->data['message'],
            'subject' => str_replace('{name}', $this->data['name'], $this->templates['subject'] ?? 'New Message from {name}'),
        ];
    }
}
