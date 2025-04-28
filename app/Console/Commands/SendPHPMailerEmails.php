<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\PHPMailerService;
use Carbon\Carbon;

class SendPHPMailerEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:send-phpmailer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process and send queued emails using PHPMailer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to process queued emails with PHPMailer...');
        
        try {
            // Get emails from the queue that need to be sent
            $queueName = config('queue.default_email_queue', 'emails');
            $batchSize = config('queue.email_batch_size', 20);
            
            $emails = DB::table('jobs')
                ->where('queue', $queueName)
                ->orderBy('created_at')
                ->limit($batchSize) // Process in batches to avoid timeouts
                ->get();
                
            if ($emails->isEmpty()) {
                $this->info('No emails in queue to process.');
                return 0;
            }
            
            $mailerService = new PHPMailerService();
            $successCount = 0;
            $failCount = 0;
            
            // Get retry settings from config or env
            $maxRetries = config('queue.email_max_retries', env('MAIL_MAX_RETRIES', 3));
            
            foreach ($emails as $email) {
                $payload = json_decode($email->payload, true);
                
                if (!isset($payload['data']['command'])) {
                    $this->error("Invalid email payload for job ID: {$email->id}");
                    DB::table('jobs')->where('id', $email->id)->delete();
                    $failCount++;
                    continue;
                }
                
                $command = unserialize($payload['data']['command']);
                
                // Check if this is a notification job
                if (isset($command->notification) && isset($command->notifiable)) {
                    $notification = $command->notification;
                    $notifiable = $command->notifiable;
                    
                    $this->info("Processing notification: " . get_class($notification) . " for " . get_class($notifiable));
                    
                    // Check if this is our contact form notification
                    if ($notification instanceof \App\Notifications\SendContactForm) {
                        // Get the notification data
                        $data = $notification->toArray($notifiable);
                        
                        // Get method reflection to call the protected method
                        $reflectionMethod = new \ReflectionMethod($notification, 'sendWithPHPMailer');
                        $reflectionMethod->setAccessible(true);
                        
                        // Get subject from array data
                        $subject = $data['subject'];
                        
                        // Create greeting and signature
                        $ownerName = env('OWNERNAME', 'Jehad');
                        $greeting = "Hello " . $ownerName;
                        $signature = "Regards, JADCO Team";
                        
                        // Send the email
                        $sent = $reflectionMethod->invoke(
                            $notification, 
                            $notifiable, 
                            $subject, 
                            $greeting, 
                            $signature
                        );
                        
                        if ($sent) {
                            $this->info("Contact form email sent successfully to: " . ($notifiable->email ?? env('OWNER_EMAIL')));
                            DB::table('jobs')->where('id', $email->id)->delete();
                            $successCount++;
                        } else {
                            $this->error("Failed to send contact form email");
                            $failCount++;
                            
                            // If we've tried too many times, move to failed jobs
                            if ($email->attempts >= $maxRetries) {
                                $this->error("Moving job to failed queue after {$email->attempts} attempts");
                                DB::table('jobs')->where('id', $email->id)->delete();
                                DB::table('failed_jobs')->insert([
                                    'uuid' => \Illuminate\Support\Str::uuid(),
                                    'connection' => $email->connection,
                                    'queue' => $email->queue,
                                    'payload' => $email->payload,
                                    'exception' => json_encode(['message' => 'Failed to send after multiple attempts']),
                                    'failed_at' => Carbon::now(),
                                ]);
                            }
                        }
                    } else {
                        // Get mail data from other notification types
                        $mailData = $this->getMailDataFromNotification($notification, $notifiable);
                        
                        if ($mailData) {
                            // Log mail data for debugging
                            $this->info("Preparing to send email with data: " . json_encode([
                                'to' => $mailData['to'],
                                'subject' => $mailData['subject'],
                                'has_body' => !empty($mailData['body']),
                                'replyTo' => $mailData['replyTo']
                            ]));
                            
                            // Send the email using PHPMailer
                            $sent = $mailerService->send(
                                $mailData['to'],
                                $mailData['subject'],
                                $mailData['body'],
                                $mailData['altBody'] ?? '',
                                $mailData['attachments'] ?? [],
                                $mailData['replyTo'] ?? [],
                                $mailData['cc'] ?? [],
                                $mailData['bcc'] ?? []
                            );
                            
                            if ($sent) {
                                $this->info("Email sent successfully to: {$mailData['to']}");
                                DB::table('jobs')->where('id', $email->id)->delete();
                                $successCount++;
                            } else {
                                $this->error("Failed to send email to: {$mailData['to']}");
                                $failCount++;
                                
                                // If we've tried too many times, move to failed jobs
                                if ($email->attempts >= $maxRetries) {
                                    $this->error("Moving job to failed queue after {$email->attempts} attempts");
                                    DB::table('jobs')->where('id', $email->id)->delete();
                                    DB::table('failed_jobs')->insert([
                                        'uuid' => \Illuminate\Support\Str::uuid(),
                                        'connection' => $email->connection,
                                        'queue' => $email->queue,
                                        'payload' => $email->payload,
                                        'exception' => json_encode(['message' => 'Failed to send after multiple attempts']),
                                        'failed_at' => Carbon::now(),
                                    ]);
                                }
                            }
                        } else {
                            $this->error("Could not extract mail data from notification");
                            $failCount++;
                        }
                    }
                } else {
                    $this->error("Unsupported job type in email queue");
                    DB::table('jobs')->where('id', $email->id)->delete();
                    $failCount++;
                }
            }
            
            $this->info("Completed processing emails. Success: {$successCount}, Failed: {$failCount}");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error processing emails: {$e->getMessage()}");
            Log::error("SendPHPMailerEmails command error: {$e->getMessage()}");
            return 1;
        }
    }
    
    /**
     * Extract mail data from a notification object
     * 
     * @param mixed $notification The notification object
     * @param mixed $notifiable The notifiable entity
     * @return array|null Mail data array or null if extraction failed
     */
    protected function getMailDataFromNotification($notification, $notifiable)
    {
        try {
            // Get the mail representation from the notification
            $mailMessage = $notification->toMail($notifiable);
            
            // Convert Laravel MailMessage to PHPMailer format
            $to = $notifiable->email ?? 
                  $notifiable->mail ?? 
                  config('mail.admin_email', env('MAIL_ADMIN_EMAIL')) ?? 
                  env('OWNER_EMAIL');
            
            // Extract subject from MailMessage
            $subject = $mailMessage->subject;
            
            // Build HTML body from MailMessage components
            $body = '<div style="font-family: Arial, sans-serif; line-height: 1.6;">';
            
            // Add greeting
            if (!empty($mailMessage->greeting)) {
                $body .= '<h2 style="margin-bottom: 20px;">' . $mailMessage->greeting . '</h2>';
            }
            
            // Add intro lines
            foreach ($mailMessage->introLines as $line) {
                $body .= '<p>' . $line . '</p>';
            }
            
            // Add action button if present
            if ($mailMessage->actionText && $mailMessage->actionUrl) {
                $body .= '<div style="margin: 30px 0;">';
                $body .= '<a href="' . $mailMessage->actionUrl . '" style="background-color: #3490dc; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-weight: bold;">' . $mailMessage->actionText . '</a>';
                $body .= '</div>';
            }
            
            // Add outro lines
            foreach ($mailMessage->outroLines as $line) {
                $body .= '<p>' . $line . '</p>';
            }
            
            // Add salutation
            if (!empty($mailMessage->salutation)) {
                $body .= '<p style="margin-top: 30px;">' . $mailMessage->salutation . '</p>';
            } else {
                $body .= '<p style="margin-top: 30px;">Regards,<br>' . config('app.name') . '</p>';
            }
            
            $body .= '</div>';
            
            // Create plain text version
            $altBody = strip_tags(str_replace(['<br>', '<br/>'], "\n", $body));
            
            // Extract reply-to, cc, bcc from MailMessage if available
            $replyTo = [];
            $cc = [];
            $bcc = [];
            
            // Check if we have headers in the MailMessage
            if (property_exists($mailMessage, 'headers') && !empty($mailMessage->headers)) {
                foreach ($mailMessage->headers as $header => $value) {
                    if (strtolower($header) === 'reply-to') {
                        $replyTo = [$value => ''];
                    }
                }
            }
            
            // Check if we have replyTo in the MailMessage
            if (property_exists($mailMessage, 'replyTo') && !empty($mailMessage->replyTo)) {
                foreach ($mailMessage->replyTo as $address => $name) {
                    $replyTo[$address] = $name;
                }
            }
            
            // Check if we have cc in the MailMessage
            if (property_exists($mailMessage, 'cc') && !empty($mailMessage->cc)) {
                foreach ($mailMessage->cc as $address) {
                    $cc[] = $address;
                }
            }
            
            // Check if we have bcc in the MailMessage
            if (property_exists($mailMessage, 'bcc') && !empty($mailMessage->bcc)) {
                foreach ($mailMessage->bcc as $address) {
                    $bcc[] = $address;
                }
            }
            
            // Add admin BCC if configured
            $adminEmail = config('mail.admin_email', env('MAIL_ADMIN_EMAIL'));
            if ($adminEmail && config('mail.admin_bcc', false) && $to !== $adminEmail) {
                $bcc[] = $adminEmail;
            }
            
            return [
                'to' => $to,
                'subject' => $subject,
                'body' => $body,
                'altBody' => $altBody,
                'attachments' => [], // No attachments by default
                'replyTo' => $replyTo,
                'cc' => $cc,
                'bcc' => $bcc
            ];
        } catch (\Exception $e) {
            Log::error("Error extracting mail data: {$e->getMessage()}");
            return null;
        }
    }
}
