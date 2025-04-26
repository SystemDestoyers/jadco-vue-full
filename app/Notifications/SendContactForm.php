<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Cache;

class SendContactForm extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $data;
    protected $useQueue;
    protected $templates;

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
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
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
        $ownerName = env('OWNERNAME', 'Mohamed Al-Qahtani');
        
        // Process template placeholders
        $subject = str_replace('{name}', $this->data['name'], $this->templates['subject'] ?? 'New Message from {name}');
        $greeting = str_replace('{owner}', $ownerName, $this->templates['greeting'] ?? 'Hello {owner}');
        $signature = $this->templates['signature'] ?? 'Regards, JADCO Team';
        
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
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
