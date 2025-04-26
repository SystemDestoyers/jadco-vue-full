<?php
/**
 * Database backup script
 * 
 * This script contains all the commands for resetting the database to its initial state.
 * It is NOT called directly by the controller (we use the Artisan command db:reset instead).
 * 
 * This file is kept for reference or manual execution if needed.
 * 
 * To run manually:
 * - From command line: php backup.php
 * - Direct access is blocked for security reasons
 */

// Check if being run from CLI
$isCLI = (php_sapi_name() === 'cli');

// Prevent web access for security
if (!$isCLI) {
    die('Direct access to this script is not allowed. Please use the database reset functionality from the admin dashboard.');
}

// Only continue if running from CLI
echo "=== Database Reset Tool ===\n\n";
echo "WARNING: Running migrations fresh will DELETE ALL DATA\n";
echo "Press Ctrl+C to cancel or Enter to continue...\n";
fgets(STDIN);

// Bootstrap Laravel application if we're continuing
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Function to run a command and output the result
function runCommand($command, $description) {
    echo "\n=== $description ===\n";
    $output = shell_exec($command . ' 2>&1');
    echo $output . "\n";
}

// Run migrations fresh with seed
runCommand('php artisan migrate:fresh --seed --force', 'Running migrations fresh with seed');

// Storage link
runCommand('php artisan storage:link --force', 'Creating storage link');

// Clear all caches
runCommand('php artisan cache:clear', 'Clearing cache');
runCommand('php artisan view:clear', 'Clearing view cache');
runCommand('php artisan route:clear', 'Clearing route cache');
runCommand('php artisan config:clear', 'Clearing config cache');
runCommand('php artisan optimize:clear', 'Clearing optimization cache');

// Reload autoloader
runCommand('composer dump-autoload', 'Reloading autoloader');

echo "\n=== Database reset completed successfully! ===\n";
echo "\nAdmin credentials:\n";
echo "Email: jad@jadco.co\n";
echo "Password: 111\n"; 