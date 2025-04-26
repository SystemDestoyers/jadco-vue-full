<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear settings table first
        DB::table('settings')->truncate();
        
        // Appearance Settings
        $this->createAppearanceSettings();
        
        // Localization Settings
        $this->createLocalizationSettings();
        
        // Email Settings
        $this->createEmailSettings();
        
        // System Settings
        $this->createSystemSettings();
    }
    
    /**
     * Create appearance settings
     */
    private function createAppearanceSettings(): void
    {
        $settings = [
            [
                'key' => 'appearance.theme',
                'name' => 'Default Theme',
                'description' => 'Default theme for the application',
                'type' => 'string',
                'value' => 'light',
                'order' => 10,
                'is_public' => true,
            ],
            [
                'key' => 'appearance.admin_theme',
                'name' => 'Admin Theme',
                'description' => 'Default theme for the admin panel',
                'type' => 'string',
                'value' => 'light',
                'order' => 20,
            ],
            [
                'key' => 'appearance.primary_color',
                'name' => 'Primary Color',
                'description' => 'Primary color for the application (hex)',
                'type' => 'string',
                'value' => '#3d8bfd',
                'order' => 30,
                'is_public' => true,
            ],
            [
                'key' => 'appearance.secondary_color',
                'name' => 'Secondary Color',
                'description' => 'Secondary color for the application (hex)',
                'type' => 'string',
                'value' => '#6c757d',
                'order' => 40,
                'is_public' => true,
            ],
            [
                'key' => 'appearance.enable_dark_mode',
                'name' => 'Enable Dark Mode Option',
                'description' => 'Allow users to switch to dark mode',
                'type' => 'boolean',
                'value' => true,
                'order' => 50,
                'is_public' => true,
            ],
            [
                'key' => 'appearance.logo',
                'name' => 'Site Logo',
                'description' => 'Main logo image path',
                'type' => 'string',
                'value' => '/images/logo.png',
                'order' => 60,
                'is_public' => true,
            ],
            [
                'key' => 'appearance.favicon',
                'name' => 'Favicon',
                'description' => 'Favicon image path',
                'type' => 'string',
                'value' => '/favicon.ico',
                'order' => 70,
                'is_public' => true,
            ],
        ];
        
        foreach ($settings as $setting) {
            Setting::create(array_merge($setting, ['group' => 'appearance']));
        }
    }
    
    /**
     * Create localization settings
     */
    private function createLocalizationSettings(): void
    {
        $settings = [
            [
                'key' => 'localization.default_language',
                'name' => 'Default Language',
                'description' => 'Default language for the application',
                'type' => 'string',
                'value' => 'en',
                'order' => 10,
                'is_public' => true,
            ],
            [
                'key' => 'localization.available_languages',
                'name' => 'Available Languages',
                'description' => 'Languages available in the application',
                'type' => 'json',
                'value' => json_encode([
                    'en' => 'English',
                    'ar' => 'Arabic'
                ]),
                'order' => 20,
                'is_public' => true,
            ],
            [
                'key' => 'localization.default_direction',
                'name' => 'Default Direction',
                'description' => 'Default text direction (ltr or rtl)',
                'type' => 'string',
                'value' => 'ltr',
                'order' => 30,
                'is_public' => true,
            ],
            [
                'key' => 'localization.auto_detect_language',
                'name' => 'Auto-detect Language',
                'description' => 'Automatically detect user language preference',
                'type' => 'boolean',
                'value' => true,
                'order' => 40,
                'is_public' => true,
            ],
            [
                'key' => 'localization.date_format',
                'name' => 'Date Format',
                'description' => 'Default date format',
                'type' => 'string',
                'value' => 'Y-m-d',
                'order' => 50,
                'is_public' => true,
            ],
            [
                'key' => 'localization.time_format',
                'name' => 'Time Format',
                'description' => 'Default time format',
                'type' => 'string',
                'value' => 'H:i',
                'order' => 60,
                'is_public' => true,
            ],
            [
                'key' => 'localization.timezone',
                'name' => 'Timezone',
                'description' => 'Default timezone',
                'type' => 'string',
                'value' => 'UTC',
                'order' => 70,
                'is_public' => true,
            ],
        ];
        
        foreach ($settings as $setting) {
            Setting::create(array_merge($setting, ['group' => 'localization']));
        }
    }
    
    /**
     * Create email settings
     */
    private function createEmailSettings(): void
    {
        $settings = [
            [
                'key' => 'email.use_queue',
                'name' => 'Use Queue for Emails',
                'description' => 'Process emails in background queue',
                'type' => 'boolean',
                'value' => true,
                'order' => 10,
            ],
            [
                'key' => 'email.smtp_host',
                'name' => 'SMTP Host',
                'description' => 'SMTP server hostname',
                'type' => 'string',
                'value' => env('MAIL_HOST', ''),
                'order' => 20,
            ],
            [
                'key' => 'email.smtp_port',
                'name' => 'SMTP Port',
                'description' => 'SMTP server port',
                'type' => 'string',
                'value' => env('MAIL_PORT', ''),
                'order' => 30,
            ],
            [
                'key' => 'email.smtp_username',
                'name' => 'SMTP Username',
                'description' => 'SMTP authentication username',
                'type' => 'string',
                'value' => env('MAIL_USERNAME', ''),
                'order' => 40,
            ],
            [
                'key' => 'email.smtp_password',
                'name' => 'SMTP Password',
                'description' => 'SMTP authentication password',
                'type' => 'string',
                'value' => env('MAIL_PASSWORD', ''),
                'order' => 50,
            ],
            [
                'key' => 'email.smtp_encryption',
                'name' => 'SMTP Encryption',
                'description' => 'SMTP encryption type (tls, ssl, or none)',
                'type' => 'string',
                'value' => env('MAIL_ENCRYPTION', 'tls'),
                'order' => 60,
            ],
            [
                'key' => 'email.from_address',
                'name' => 'From Address',
                'description' => 'Default email from address',
                'type' => 'string',
                'value' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
                'order' => 70,
            ],
            [
                'key' => 'email.from_name',
                'name' => 'From Name',
                'description' => 'Default email from name',
                'type' => 'string',
                'value' => env('MAIL_FROM_NAME', 'JADCO'),
                'order' => 80,
            ],
            [
                'key' => 'email.admin_email',
                'name' => 'Admin Email',
                'description' => 'Email to receive notifications',
                'type' => 'string',
                'value' => env('MAIL_ADMIN_EMAIL', 'admin@example.com'),
                'order' => 90,
            ],
            [
                'key' => 'email.contact_form_subject',
                'name' => 'Contact Form Subject',
                'description' => 'Email subject for contact form submissions',
                'type' => 'string',
                'value' => 'New Message from {name}',
                'order' => 100,
            ],
            [
                'key' => 'email.contact_form_greeting',
                'name' => 'Contact Form Greeting',
                'description' => 'Email greeting for contact form submissions',
                'type' => 'string',
                'value' => 'Hello {owner}',
                'order' => 110,
            ],
            [
                'key' => 'email.contact_form_success_message',
                'name' => 'Contact Form Success Message',
                'description' => 'Message shown after successful form submission',
                'type' => 'string',
                'value' => 'Thank you for your message! We will get back to you soon.',
                'order' => 120,
                'is_public' => true,
            ],
            [
                'key' => 'email.contact_form_signature',
                'name' => 'Contact Form Signature',
                'description' => 'Email signature for contact form submissions',
                'type' => 'string',
                'value' => 'Regards, JADCO Team',
                'order' => 130,
            ],
        ];
        
        foreach ($settings as $setting) {
            Setting::create(array_merge($setting, ['group' => 'email']));
        }
    }
    
    /**
     * Create system settings
     */
    private function createSystemSettings(): void
    {
        $settings = [
            [
                'key' => 'system.site_name',
                'name' => 'Site Name',
                'description' => 'Name of the website',
                'type' => 'string',
                'value' => 'JADCO',
                'order' => 10,
                'is_public' => true,
            ],
            [
                'key' => 'system.site_description',
                'name' => 'Site Description',
                'description' => 'Description of the website',
                'type' => 'string',
                'value' => 'JADCO Official Website',
                'order' => 20,
                'is_public' => true,
            ],
            [
                'key' => 'system.maintenance_mode',
                'name' => 'Maintenance Mode',
                'description' => 'Put site in maintenance mode',
                'type' => 'boolean',
                'value' => false,
                'order' => 30,
            ],
            [
                'key' => 'system.maintenance_message',
                'name' => 'Maintenance Message',
                'description' => 'Message to display during maintenance',
                'type' => 'string',
                'value' => 'We are currently performing scheduled maintenance. Please check back soon.',
                'order' => 40,
                'is_public' => true,
            ],
            [
                'key' => 'system.google_analytics_id',
                'name' => 'Google Analytics ID',
                'description' => 'Google Analytics tracking ID',
                'type' => 'string',
                'value' => '',
                'order' => 50,
            ],
            [
                'key' => 'system.recaptcha_site_key',
                'name' => 'reCAPTCHA Site Key',
                'description' => 'Google reCAPTCHA site key',
                'type' => 'string',
                'value' => '',
                'order' => 60,
            ],
            [
                'key' => 'system.recaptcha_secret_key',
                'name' => 'reCAPTCHA Secret Key',
                'description' => 'Google reCAPTCHA secret key',
                'type' => 'string',
                'value' => '',
                'order' => 70,
            ],
            [
                'key' => 'system.backup_enabled',
                'name' => 'Enable Automated Backups',
                'description' => 'Schedule automatic database backups',
                'type' => 'boolean',
                'value' => false,
                'order' => 80,
            ],
            [
                'key' => 'system.backup_frequency',
                'name' => 'Backup Frequency',
                'description' => 'How often to run backups (daily, weekly, monthly)',
                'type' => 'string',
                'value' => 'daily',
                'order' => 90,
            ],
            [
                'key' => 'system.backup_retention',
                'name' => 'Backup Retention',
                'description' => 'Number of days to keep backups',
                'type' => 'integer',
                'value' => 7,
                'order' => 100,
            ],
            [
                'key' => 'system.paginate_per_page',
                'name' => 'Items Per Page',
                'description' => 'Default number of items per page in listings',
                'type' => 'integer',
                'value' => 15,
                'order' => 110,
            ],
            [
                'key' => 'system.owner_name',
                'name' => 'Owner Name',
                'description' => 'Name of the site owner (for emails)',
                'type' => 'string',
                'value' => 'Mohamed Al-Qahtani',
                'order' => 120,
            ],
        ];
        
        foreach ($settings as $setting) {
            Setting::create(array_merge($setting, ['group' => 'system']));
        }
    }
}
