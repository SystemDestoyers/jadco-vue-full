<?php

namespace Database\Seeders;

use App\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear the media table first
        Media::truncate();
        
        // Import all images from public/images and subdirectories
        $this->importAllImagesRecursively('public/images');
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
            $this->command->info("Base directory not found: {$fullPath}");
            return;
        }
        
        $this->command->info("Scanning directory: {$baseDirectory}");
        
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
            $this->command->info("Directory not found: {$fullPath}");
            return;
        }
        
        $files = File::files($fullPath);
        $importedCount = 0;
        
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
            $publicPath = '/' . $relativePath . '/' . $filename;
            
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
        
        $this->command->info("Imported {$importedCount} images from {$directory} to {$collection} collection");
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
