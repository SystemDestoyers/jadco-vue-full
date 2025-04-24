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
            $file = $request->file('file');
            $collection = $request->input('collection', 'default');
            $folder = $request->input('folder', '');
            
            // Ensure the folder exists in public/images
            $baseFolder = 'public/images';
            $targetFolder = $baseFolder;
            
            if (!empty($folder)) {
                $targetFolder .= '/' . trim($folder, '/');
                if (!File::exists($targetFolder)) {
                    File::makeDirectory($targetFolder, 0755, true);
                }
            }
            
            // Generate a unique filename or keep original name
            $useOriginalFilename = $request->input('use_original_filename', false);
            if ($useOriginalFilename) {
                $filename = $file->getClientOriginalName();
                // Check if file already exists and create unique name if needed
                if (File::exists($targetFolder . '/' . $filename)) {
                    $filename = pathinfo($filename, PATHINFO_FILENAME) . '_' . 
                                Str::random(8) . '.' . $file->getClientOriginalExtension();
                }
            } else {
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            }
            
            // Move the file to the target directory
            $file->move($targetFolder, $filename);
            
            // Create the path relative to public folder (for URL generation)
            $relativePath = str_replace('public/', '', $targetFolder) . '/' . $filename;
            
            $media = Media::create([
                'filename' => $filename,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'path' => $relativePath,
                'disk' => 'public',
                'size' => $file->getSize(),
                'collection' => $collection,
                'alt_text' => $request->input('alt_text'),
                'caption' => $request->input('caption'),
                'metadata' => [
                    'dimensions' => $this->getImageDimensions($file),
                    'folder' => $folder,
                    'last_modified' => now()->toIso8601String(),
                ],
                'created_by' => auth()->check() ? auth()->user()->name : null,
            ]);
            
            return response()->json($media, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to upload file: ' . $e->getMessage()], 500);
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
                
                $currentPath = public_path($media->path);
                $targetFolder = 'public/images/' . trim($newFolder, '/');
                
                // Ensure the target folder exists
                if (!File::exists($targetFolder)) {
                    File::makeDirectory($targetFolder, 0755, true);
                }
                
                $targetPath = $targetFolder . '/' . $media->filename;
                
                // Move file to new folder
                if (File::exists($currentPath)) {
                    File::move($currentPath, $targetPath);
                    
                    // Update path in the database
                    $relativePath = str_replace('public/', '', $targetFolder) . '/' . $media->filename;
                    $media->path = $relativePath;
                    
                    // Update metadata
                    $metadata = is_array($media->metadata) ? $media->metadata : [];
                    $metadata['folder'] = $newFolder;
                    $media->metadata = $metadata;
                }
            }
            
            // Update other properties
            $media->alt_text = $request->input('alt_text', $media->alt_text);
            $media->caption = $request->input('caption', $media->caption);
            $media->collection = $request->input('collection', $media->collection);
            $media->save();
            
            // Refresh to get updated data
            $media->refresh();
            
            return response()->json($media);
        } catch (\Exception $e) {
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
        
        $folder = trim($request->folder, '/');
        $path = 'public/images/' . $folder;
        
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
            return response()->json(['message' => 'Folder created successfully'], 201);
        }
        
        return response()->json(['message' => 'Folder already exists'], 200);
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
            if (!File::exists($basePath)) {
                File::makeDirectory($basePath, 0755, true);
            }
            
            // Get all folders
            $allFolders = $this->scanFoldersRecursively($basePath);
            
            // Add root folder
            array_unshift($allFolders, [
                'name' => 'Root',
                'path' => '',
                'full_path' => 'images',
                'children' => []
            ]);
            
            return response()->json($allFolders);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to list folders: ' . $e->getMessage()], 500);
        }
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
        
        if (!File::exists($path)) {
            return $result;
        }
        
        $directories = File::directories($path);
        
        foreach ($directories as $directory) {
            $name = basename($directory);
            $relPath = $relativePath ? "{$relativePath}/{$name}" : $name;
            
            // Skip hidden folders
            if (str_starts_with($name, '.')) {
                continue;
            }
            
            // Get file count in this directory
            $fileCount = count(array_filter(
                File::files($directory),
                fn($file) => !str_starts_with(basename($file), '.') && 
                              str_starts_with(File::mimeType($file), 'image/')
            ));
            
            $result[] = [
                'name' => $name,
                'path' => $relPath,
                'full_path' => 'images/' . $relPath,
                'file_count' => $fileCount,
                'children' => $this->scanFoldersRecursively($directory, $relPath)
            ];
        }
        
        return $result;
    }
    
    /**
     * Get image dimensions if file is an image.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return array|null
     */
    private function getImageDimensions($file)
    {
        if (str_starts_with($file->getMimeType(), 'image/')) {
            try {
                list($width, $height) = getimagesize($file->getPathname());
                return compact('width', 'height');
            } catch (\Exception $e) {
                return null;
            }
        }
        
        return null;
    }
}
