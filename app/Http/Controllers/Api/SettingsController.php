<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SendContactForm;
use App\Models\Setting;

class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     * 
     * @return void
     */
    public function __construct()
    {
        // Initialize default settings if they don't exist
        $this->initializeDefaultSettings();
    }

    /**
     * Initialize default settings if they don't exist
     * 
     * @return void
     */
    protected function initializeDefaultSettings()
    {
        // Email queue setting - default to true
        if (!Cache::has('use_email_queue')) {
            Cache::forever('use_email_queue', true);
        }

        // SMTP settings
        if (!Cache::has('smtp_settings')) {
            // Get default settings from .env
            $smtpSettings = [
                'host' => env('MAIL_HOST', ''),
                'port' => env('MAIL_PORT', ''),
                'username' => env('MAIL_USERNAME', ''),
                'password' => env('MAIL_PASSWORD', ''),
                'encryption' => env('MAIL_ENCRYPTION', 'tls'),
                'from_address' => env('MAIL_FROM_ADDRESS', ''),
                'from_name' => env('MAIL_FROM_NAME', ''),
                'admin_email' => env('MAIL_ADMIN_EMAIL', '')
            ];
            
            Cache::forever('smtp_settings', $smtpSettings);
        }

        // Email templates
        if (!Cache::has('email_templates')) {
            $templates = [
                'subject' => 'New Message from {name}',
                'greeting' => 'Hello {owner}',
                'success_message' => 'Thank you for your message! We will get back to you soon.',
                'signature' => 'Regards, JADCO Team'
            ];
            
            Cache::forever('email_templates', $templates);
        }
    }

    /**
     * Update email settings
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateEmailSettings(Request $request)
    {
        try {
            $validated = $request->validate([
                'use_email_queue' => 'required|boolean',
                'smtp' => 'sometimes|array',
                'smtp.host' => 'sometimes|string',
                'smtp.port' => 'sometimes|string',
                'smtp.username' => 'sometimes|string',
                'smtp.password' => 'sometimes|string',
                'smtp.encryption' => 'sometimes|string|in:tls,ssl,none',
                'smtp.from_address' => 'sometimes|string',
                'smtp.from_name' => 'sometimes|string',
                'smtp.admin_email' => 'sometimes|string|email',
                'templates' => 'sometimes|array',
                'templates.subject' => 'sometimes|string',
                'templates.greeting' => 'sometimes|string',
                'templates.success_message' => 'sometimes|string',
                'templates.signature' => 'sometimes|string',
            ]);

            // Store queue setting
            Cache::forever('use_email_queue', $validated['use_email_queue']);
            
            // Store SMTP settings if provided
            if (isset($validated['smtp'])) {
                // Get existing settings first
                $existingSettings = Cache::get('smtp_settings', []);
                
                // Merge with new settings (preserving password if not provided)
                $smtpSettings = array_merge($existingSettings, $validated['smtp']);
                
                // Update in cache
                Cache::forever('smtp_settings', $smtpSettings);
                
                // Optionally update .env file (not recommended for security reasons)
                // You could implement a method here to update the .env file if needed
            }
            
            // Store template settings if provided
            if (isset($validated['templates'])) {
                $existingTemplates = Cache::get('email_templates', []);
                $templates = array_merge($existingTemplates, $validated['templates']);
                Cache::forever('email_templates', $templates);
            }

            // Log the change
            Log::info('Email settings updated', [
                'use_email_queue' => $validated['use_email_queue'],
                'smtp_updated' => isset($validated['smtp']),
                'templates_updated' => isset($validated['templates']),
                'user' => auth()->check() ? auth()->id() : 'unauthenticated'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email settings updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update email settings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test SMTP connection
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function testEmailConnection(Request $request)
    {
        try {
            $validated = $request->validate([
                'smtp' => 'required|array',
                'smtp.host' => 'required|string',
                'smtp.port' => 'required|string',
                'smtp.username' => 'required|string',
                'smtp.password' => 'required|string',
                'smtp.encryption' => 'required|string|in:tls,ssl,none',
                'smtp.from_address' => 'required|string|email',
                'smtp.from_name' => 'required|string',
                'smtp.admin_email' => 'required|string|email',
            ]);

            $smtpSettings = $validated['smtp'];
            
            // Configure mail with provided settings
            $encryption = $smtpSettings['encryption'] !== 'none' ? $smtpSettings['encryption'] : null;
            
            Config::set('mail.mailers.smtp.host', $smtpSettings['host']);
            Config::set('mail.mailers.smtp.port', $smtpSettings['port']);
            Config::set('mail.mailers.smtp.encryption', $encryption);
            Config::set('mail.mailers.smtp.username', $smtpSettings['username']);
            Config::set('mail.mailers.smtp.password', $smtpSettings['password']);
            Config::set('mail.from.address', $smtpSettings['from_address']);
            Config::set('mail.from.name', $smtpSettings['from_name']);
            
            // Create test data for email
            $testData = [
                'name' => 'Test User',
                'email' => $smtpSettings['from_address'],
                'phone' => 'Test Phone',
                'message' => 'This is a test message sent from JADCO website to verify SMTP settings.'
            ];
            
            // Send the test notification
            $result = Notification::route('mail', $smtpSettings['admin_email'])
                ->notify(new SendContactForm($testData));
            
            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send test email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all settings
     * 
     * @return \Illuminate\Http\Response
     */
    public function getSettings()
    {
        // Get all settings from cache
        $useEmailQueue = Cache::get('use_email_queue', true);
        $smtpSettings = Cache::get('smtp_settings', []);
        $emailTemplates = Cache::get('email_templates', []);
        
        // Remove sensitive data from response (password)
        if (isset($smtpSettings['password']) && !empty($smtpSettings['password'])) {
            $smtpSettings['password'] = '••••••••••••'; // Hide actual password, only show placeholder
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'email' => [
                    'use_email_queue' => $useEmailQueue,
                    'smtp' => $smtpSettings,
                    'templates' => $emailTemplates
                ]
            ]
        ]);
    }

    /**
     * Get all database settings
     * 
     * @return \Illuminate\Http\Response
     */
    public function getDatabaseSettings()
    {
        try {
            $settings = Setting::orderBy('group')->orderBy('order')->get();
            
            return response()->json([
                'success' => true,
                'data' => $settings
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get database settings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get database settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update database settings
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateDatabaseSettings(Request $request)
    {
        try {
            $validated = $request->validate([
                'settings' => 'required|array'
            ]);
            
            $settingsData = $validated['settings'];
            $updated = 0;
            $errors = [];
            
            // Update each setting
            foreach ($settingsData as $key => $value) {
                try {
                    $success = Setting::set($key, $value);
                    if ($success) {
                        $updated++;
                    } else {
                        $errors[] = "Setting '$key' not found";
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error updating '$key': " . $e->getMessage();
                    Log::error('Error updating setting', [
                        'key' => $key,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Clear settings cache
            Cache::forget('settings');
            
            return response()->json([
                'success' => true,
                'message' => "$updated settings updated successfully",
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update database settings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update database settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run database backup process
     * 
     * @return \Illuminate\Http\Response
     */
    public function runDatabaseBackup()
    {
        try {
            // Check if user is logged in
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to backup function'
                ], 403);
            }
            
            // Run database reset command and capture output
            \Artisan::call('db:reset');
            $output = \Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => 'Database has been successfully reset and seeded.',
                'details' => $output
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to run database backup', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to run database backup: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Reset images by copying from backup folder to images folder
     * 
     * @return \Illuminate\Http\Response
     */
    public function resetImages()
    {
        try {
            // Check if user is logged in
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to image reset function'
                ], 403);
            }
            
            $backupPath = public_path('backup/images');
            $imagesPath = public_path('images');
            
            // Ensure backup directory exists, create it if it doesn't
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
                
                // If we just created the backup directory, copy files from images to backup
                // This ensures we have files to restore from in the future
                if (file_exists($imagesPath) && is_dir($imagesPath)) {
                    $this->copyDirectory($imagesPath, $backupPath);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Backup directory created and populated with current images. Please run the reset operation again if needed.'
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Both backup and images directories do not exist. Please ensure at least one contains images.'
                    ], 404);
                }
            }
            
            // Make sure images directory exists
            if (!file_exists($imagesPath)) {
                mkdir($imagesPath, 0755, true);
            }
            
            // Delete all files in the images directory
            $this->deleteDirectory($imagesPath);
            
            // Ensure the images directory exists after deletion
            if (!file_exists($imagesPath)) {
                mkdir($imagesPath, 0755, true);
            }
            
            // Copy all files from backup to images directory
            $this->copyDirectory($backupPath, $imagesPath);
            
            return response()->json([
                'success' => true,
                'message' => 'All images have been successfully reset from backup.'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to reset images', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to reset images: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Helper method to delete a directory and all its contents
     * 
     * @param string $dirPath
     * @return bool
     */
    private function deleteDirectory($dirPath) 
    {
        if (!is_dir($dirPath)) {
            return false;
        }
        
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dirPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        
        // Don't delete the main directory itself, just empty it
        return true;
    }
    
    /**
     * Helper method to copy a directory and all its contents
     * 
     * @param string $source
     * @param string $destination
     * @return bool
     */
    private function copyDirectory($source, $destination) 
    {
        if (!is_dir($source)) {
            return false;
        }
        
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($files as $file) {
            $targetPath = $destination . '/' . $files->getSubPathName();
            
            if ($file->isDir()) {
                if (!is_dir($targetPath)) {
                    mkdir($targetPath, 0755, true);
                }
            } else {
                copy($file->getRealPath(), $targetPath);
            }
        }
        
        return true;
    }
} 