<?php

namespace App\Models;

use App\Models\Traits\FixesInvalidDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory, FixesInvalidDates;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'filename',
        'original_filename',
        'mime_type',
        'path',
        'disk',
        'size',
        'collection',
        'alt_text',
        'caption',
        'metadata',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'metadata' => 'array',
        'size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['url', 'thumbnail_url', 'is_image', 'extension'];

    /**
     * Get the full URL to the file.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url($this->path);
    }

    /**
     * Get the thumbnail URL for image files.
     *
     * @return string|null
     */
    public function getThumbnailUrlAttribute()
    {
        if (!$this->isImage()) {
            return null;
        }

        // For now, return the full image as a thumbnail
        // In a production app, you might want to generate actual thumbnails
        return $this->url;
    }

    /**
     * Check if this media is an image.
     *
     * @return bool
     */
    public function isImage()
    {
        return str_starts_with($this->mime_type, 'image/');
    }
    
    /**
     * Get the is_image attribute.
     *
     * @return bool
     */
    public function getIsImageAttribute()
    {
        return $this->isImage();
    }

    /**
     * Get the file extension.
     *
     * @return string
     */
    public function getExtensionAttribute()
    {
        return pathinfo($this->filename, PATHINFO_EXTENSION);
    }
}
