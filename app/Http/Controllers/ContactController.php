<?php

namespace App\Http\Controllers;

use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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
            $adminEmail = config('mail.admin_email', 'admin@example.com');
            
            // You can create a proper mail class for this
            Mail::raw(
                "New contact form submission:\n\n" .
                "Name: {$message->first_name} {$message->last_name}\n" .
                "Email: {$message->email}\n" .
                "Phone: {$message->phone}\n\n" .
                "Message:\n{$message->message}\n\n" .
                "View in admin panel: " . url('/admin/messages'),
                function ($mail) use ($message, $adminEmail) {
                    $mail->to($adminEmail)
                        ->subject("New Contact Form Message from {$message->first_name} {$message->last_name}");
                }
            );
        } catch (\Exception $e) {
            // Just log the error but don't break the form submission
            Log::warning('Failed to send email notification for contact form', [
                'message_id' => $message->id,
                'error' => $e->getMessage()
            ]);
        }
    }
} 