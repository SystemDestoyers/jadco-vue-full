<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    /**
     * Display a listing of the media.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Build the query with all filters
        $query = Media::query();
        
        // Apply filters if provided
        if ($request->has('collection') && $request->collection) {
            $query->where('collection', $request->collection);
        }
        
        if ($request->has('type') && $request->type) {
            $query->where('mime_type', 'like', $request->type . '%');
        }
        
        if ($request->has('folder') && $request->folder) {
            $folder = $request->folder;
            $query->whereJsonContains('metadata->folder', $folder);
        }
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('filename', 'like', "%{$search}%")
                  ->orWhere('original_filename', 'like', "%{$search}%")
                  ->orWhere('alt_text', 'like', "%{$search}%");
            });
        }
        
        // Get the pagination limit from request or default to 24
        $perPage = (int) $request->input('per_page', 24);
        
        // Order by newest first
        $query->orderBy('created_at', 'desc');
        
        // Get paginated results
        $media = $query->paginate($perPage);
        
        // Add URL to each media item
        $media->getCollection()->transform(function ($item) {
            // Add the full URL to access the image
            $item->url = url($item->path);
            
            // Add a thumbnail URL for display in the media library
            if (str_starts_with($item->mime_type, 'image/')) {
                $item->thumbnail = url($item->path);
            } else {
                $item->thumbnail = null;
            }
            
            return $item;
        });
        
        return response()->json($media);
    }

    /**
     * Store a newly created media file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'folder' => 'nullable|string|max:255',
            'collection' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string',
            'caption' => 'nullable|string',
        ]);
        
        try {
            \Log::info('Starting media upload process');
            
            // Very basic direct file handling
            if (!$request->hasFile('file') || !$request->file('file')->isValid()) {
                return response()->json(['error' => 'Invalid file upload'], 400);
            }
            
            // Get the uploaded file
            $uploadedFile = $request->file('file');
            $tempFilePath = $uploadedFile->getRealPath();
            
            // Log temp file info
            \Log::info('Temp file path: ' . $tempFilePath);
            \Log::info('Temp file exists: ' . (file_exists($tempFilePath) ? 'Yes' : 'No'));
            
            // Get upload parameters
            $collection = $request->input('collection', 'general');
            $folder = $request->input('folder', '');
            $originalName = $uploadedFile->getClientOriginalName();
            $extension = $uploadedFile->getClientOriginalExtension();
            
            // Generate a filename without special characters
            $safeFilename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
            $filename = $safeFilename . '_' . time() . '.' . $extension;
            
            // Create target directory path
            $targetDir = 'images';
            if (!empty($folder)) {
                $targetDir .= '/' . trim($folder, '/');
            }
            
            // Full path to target directory
            $fullTargetDir = public_path($targetDir);
            
            // Create directory if needed
            if (!file_exists($fullTargetDir)) {
                mkdir($fullTargetDir, 0755, true);
            }
            
            // Full destination path for the file
            $destPath = $fullTargetDir . '/' . $filename;
            
            // Copy file directly
            \Log::info('Copying file from ' . $tempFilePath . ' to ' . $destPath);
            $copySuccess = copy($tempFilePath, $destPath);
            
            if (!$copySuccess || !file_exists($destPath)) {
                \Log::error('Failed to copy file to destination');
                return response()->json(['error' => 'Failed to save file. Copy operation failed.'], 500);
            }
            
            // Ensure proper permissions
            chmod($destPath, 0644);
            
            // Get file dimensions
            $dimensions = null;
            if (str_starts_with($uploadedFile->getMimeType(), 'image/')) {
                try {
                    list($width, $height) = getimagesize($destPath);
                    $dimensions = ['width' => $width, 'height' => $height];
                } catch (\Exception $e) {
                    \Log::warning('Failed to get image dimensions: ' . $e->getMessage());
                }
            }
            
            // Force a valid past date
            $validDate = '2023' . substr(date('Y-m-d H:i:s'), 4);
            
            // Database path (relative to public)
            $dbPath = $targetDir . '/' . $filename;
            
            // Create media record
            $media = new Media();
            $media->filename = $filename;
            $media->original_filename = $originalName;
            $media->mime_type = $uploadedFile->getMimeType();
            $media->path = $dbPath;
            $media->disk = 'public';
            $media->size = $uploadedFile->getSize();
            $media->collection = $collection;
            $media->alt_text = $request->input('alt_text');
            $media->caption = $request->input('caption');
            $media->metadata = [
                'dimensions' => $dimensions,
                'folder' => $folder,
                'last_modified' => $validDate,
            ];
            $media->created_by = auth()->check() ? auth()->user()->name : 'Guest';
            
            // Set timestamps
            $media->created_at = $validDate;
            $media->updated_at = $validDate;
            
            $media->save(['timestamps' => false]);
            
            // Reload for URL generation
            $media->refresh();
            
            \Log::info('Media upload successful: ' . $media->id);
            
            return response()->json($media, 201);
            
        } catch (\Exception $e) {
            \Log::error('Media upload error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Failed to upload file',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified media.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $media = Media::findOrFail($id);
            return response()->json($media);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Media not found'], 404);
        }
    }

    /**
     * Update the specified media.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'alt_text' => 'nullable|string',
            'caption' => 'nullable|string',
            'collection' => 'nullable|string|max:255',
            'folder' => 'nullable|string|max:255',
        ]);
        
        try {
            $media = Media::findOrFail($id);
            
            // If folder was changed, move the file
            $newFolder = $request->input('folder');
            if ($newFolder !== null && 
                (!isset($media->metadata['folder']) || $newFolder !== $media->metadata['folder'])) {
                
                // Current file path
                $currentPath = public_path($media->path);
                
                // New destination path
                $newUploadPath = 'images';
                if (!empty($newFolder)) {
                    $newUploadPath .= '/' . trim($newFolder, '/');
                }
                
                // Create directory if it doesn't exist
                $newFullPath = public_path($newUploadPath);
                if (!File::exists($newFullPath)) {
                    File::makeDirectory($newFullPath, 0755, true);
                }
                
                // Move file to new location
                $newFilePath = $newFullPath . '/' . $media->filename;
                
                if (File::exists($currentPath)) {
                    File::copy($currentPath, $newFilePath);
                    File::delete($currentPath);
                    
                    // Update path in database
                    $newDbPath = $newUploadPath . '/' . $media->filename;
                    $media->path = $newDbPath;
                    
                    // Update metadata
                    $metadata = is_array($media->metadata) ? $media->metadata : [];
                    $metadata['folder'] = $newFolder;
                    
                    // Force a valid date
                    $validDate = '2023' . substr(date('Y-m-d H:i:s'), 4);
                    $metadata['last_modified'] = $validDate;
                    
                    $media->metadata = $metadata;
                }
            }
            
            // Update other properties
            $media->alt_text = $request->input('alt_text', $media->alt_text);
            $media->caption = $request->input('caption', $media->caption);
            $media->collection = $request->input('collection', $media->collection);
            
            // Force a valid past date for updated_at
            $validDate = '2023' . substr(date('Y-m-d H:i:s'), 4);
            $media->updated_at = $validDate;
            
            // Save without auto-updating timestamps
            $media->save(['timestamps' => false]);
            
            // Refresh to get updated data
            $media->refresh();
            
            return response()->json($media);
        } catch (\Exception $e) {
            \Log::error('Media update error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update media: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified media.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $media = Media::findOrFail($id);
        
        // Delete the file from public directory
        $filePath = public_path($media->path);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }
        
        // Delete the database record
        $media->delete();
        
        return response()->json(null, 204);
    }
    
    /**
     * Create folder in images directory.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createFolder(Request $request)
    {
        $request->validate([
            'folder' => 'required|string|max:255',
        ]);
        
        try {
            $folder = trim($request->folder, '/');
            
            // Create path directly in the images folder without 'public/' prefix
            $path = public_path('images/' . $folder);
            
            \Log::info('Creating folder: ' . $path);
            
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
                return response()->json(['message' => 'Folder created successfully'], 201);
            }
            
            return response()->json(['message' => 'Folder already exists'], 200);
        } catch (\Exception $e) {
            \Log::error('Error creating folder: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create folder: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * List all folders in the images directory.
     *
     * @return \Illuminate\Http\Response
     */
    public function listFolders()
    {
        try {
            $basePath = public_path('images');
            
            // First make sure the base directory exists
            if (!file_exists($basePath)) {
                mkdir($basePath, 0755, true);
            }
            
            // Get all folders
            $allFolders = $this->scanFoldersRecursively($basePath);
            
            // Add root folder
            array_unshift($allFolders, [
                'name' => 'Root',
                'path' => '',
                'full_path' => 'images',
                'file_count' => $this->countImagesInDirectory($basePath),
                'children' => []
            ]);
            
            return response()->json($allFolders);
        } catch (\Exception $e) {
            \Log::error('Failed to list folders: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to list folders: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Count images in a directory.
     *
     * @param string $directory
     * @return int
     */
    private function countImagesInDirectory($directory)
    {
        if (!file_exists($directory)) {
            return 0;
        }
        
        $files = scandir($directory);
        $count = 0;
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            $path = $directory . '/' . $file;
            
            if (is_file($path) && 
                !str_starts_with($file, '.') && 
                preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i', $file)) {
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * Scan folders recursively.
     *
     * @param  string  $path
     * @param  string  $relativePath
     * @return array
     */
    private function scanFoldersRecursively($path, $relativePath = '')
    {
        $result = [];
        
        if (!file_exists($path)) {
            return $result;
        }
        
        $items = scandir($path);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            
            $fullPath = $path . '/' . $item;
            
            if (is_dir($fullPath)) {
                $name = $item;
                $relPath = $relativePath ? "{$relativePath}/{$name}" : $name;
                
                // Skip hidden folders
                if (str_starts_with($name, '.')) {
                    continue;
                }
                
                // Count image files in this directory
                $fileCount = $this->countImagesInDirectory($fullPath);
                
                $result[] = [
                    'name' => $name,
                    'path' => $relPath,
                    'full_path' => 'images/' . $relPath,
                    'file_count' => $fileCount,
                    'children' => $this->scanFoldersRecursively($fullPath, $relPath)
                ];
            }
        }
        
        return $result;
    }
}
