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
            
            // Send notification
            Notification::route('mail', $adminEmail)
                ->notify(new SendContactForm($data));
                
            // Log the successful message submission
            Log::info('Direct contact form email sent', [
                'email' => $data['email'],
                'name' => $data['name'],
                'mail_driver' => $mailDriver
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
            $adminEmail = config('mail.admin_email', 'jad@jadco.co');
            
            // Format data for notification
            $data = [
                'name' => $message->first_name . ' ' . $message->last_name,
                'email' => $message->email,
                'phone' => $message->phone,
                'message' => $message->message
            ];
            
            // Use our new notification class
            Notification::route('mail', $adminEmail)
                ->notify(new SendContactForm($data));
                
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
            
            // Send email notification if configured
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