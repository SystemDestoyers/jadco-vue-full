<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SyncImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:sync-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync images from public/images directory to the media table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting image synchronization...');

        // Check if user wants to truncate the table first
        if ($this->confirm('Do you want to clear the media table before importing? This will delete all existing media records.', false)) {
            Media::truncate();
            $this->info('Media table cleared.');
        }

        // Import all images from public/images and subdirectories
        $this->importAllImagesRecursively('public/images');

        $this->info('Image synchronization completed successfully!');
        
        return 0;
    }

    /**
     * Import images recursively from a directory and its subdirectories
     *
     * @param string $baseDirectory
     * @return void
     */
    private function importAllImagesRecursively($baseDirectory)
    {
        // Get full directory path
        $fullPath = public_path(str_replace('public/', '', $baseDirectory));
        
        if (!File::exists($fullPath)) {
            $this->error("Base directory not found: {$fullPath}");
            return;
        }
        
        $this->info("Scanning directory: {$baseDirectory}");
        
        // Process images in current directory
        $this->importImagesFromDirectory($baseDirectory, $this->getCollectionFromPath($baseDirectory));
        
        // Process subdirectories
        $subdirectories = File::directories($fullPath);
        foreach ($subdirectories as $subdirectory) {
            $relativePath = $baseDirectory . '/' . basename($subdirectory);
            $this->importAllImagesRecursively($relativePath);
        }
    }
    
    /**
     * Get collection name from path
     *
     * @param string $path
     * @return string
     */
    private function getCollectionFromPath($path)
    {
        $parts = explode('/', $path);
        $lastPart = end($parts);
        
        // Map directory names to collection names
        $collectionMap = [
            'images' => 'general',
            'services' => 'services',
            'portfolio' => 'portfolio',
            'testimonials' => 'testimonials',
            'Header' => 'headers',
            '02_Education' => 'education',
            '03_Trining' => 'training',
            '04_AI' => 'ai',
            '05_eGame' => 'gaming',
            '06_Arts' => 'arts',
        ];
        
        return $collectionMap[$lastPart] ?? strtolower($lastPart);
    }
    
    /**
     * Import images from a directory into the media library.
     *
     * @param string $directory
     * @param string $collection
     * @return void
     */
    private function importImagesFromDirectory($directory, $collection)
    {
        // Get base path without 'public'
        $relativePath = str_replace('public/', '', $directory);
        $fullPath = public_path($relativePath);
        
        if (!File::exists($fullPath)) {
            $this->error("Directory not found: {$fullPath}");
            return;
        }
        
        $files = File::files($fullPath);
        $importedCount = 0;
        $skippedCount = 0;
        
        foreach ($files as $file) {
            // Skip non-image files
            if (!in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
                continue;
            }
            
            // Skip files that start with '.' (hidden files)
            if (str_starts_with($file->getFilename(), '.')) {
                continue;
            }
            
            // Get file details
            $filename = $file->getFilename();
            $size = $file->getSize();
            $mimeType = File::mimeType($file->getPathname());
            
            // Path relative to public directory (for URL generation)
            $publicPath = $relativePath . '/' . $filename;
            
            // Check if this file already exists in the database
            $existingMedia = Media::where('path', $publicPath)->first();
            
            if ($existingMedia) {
                $skippedCount++;
                continue;
            }
            
            // Create folder from directory path
            $folder = str_replace('images/', '', $relativePath);
            
            // Create a media record
            Media::create([
                'filename' => $filename,
                'original_filename' => $filename,
                'mime_type' => $mimeType,
                'path' => $publicPath,
                'disk' => 'public',
                'size' => $size,
                'collection' => $collection,
                'alt_text' => $this->generateAltTextFromFilename($filename),
                'caption' => null,
                'metadata' => [
                    'dimensions' => $this->getImageDimensions($file->getPathname()),
                    'folder' => $folder === 'images' ? '' : $folder,
                    'last_modified' => now()->toIso8601String(),
                ],
                'created_by' => 'System',
            ]);
            
            $importedCount++;
        }
        
        $this->info("Imported {$importedCount} images from {$directory} to {$collection} collection (skipped {$skippedCount} existing files)");
    }
    
    /**
     * Generate alt text from filename.
     *
     * @param string $filename
     * @return string
     */
    private function generateAltTextFromFilename($filename)
    {
        // Remove extension
        $name = pathinfo($filename, PATHINFO_FILENAME);
        
        // Replace underscores and hyphens with spaces
        $name = str_replace(['_', '-'], ' ', $name);
        
        // Capitalize first letter of each word
        return ucwords($name);
    }
    
    /**
     * Get image dimensions.
     *
     * @param string $path
     * @return array|null
     */
    private function getImageDimensions($path)
    {
        try {
            list($width, $height) = getimagesize($path);
            return compact('width', 'height');
        } catch (\Exception $e) {
            return null;
        }
    }
} 