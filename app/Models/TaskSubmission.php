<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TaskSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'type',
        'title',
        'description',
        'file_path',
        'original_filename',
        'file_size',
        'mime_type',
        'link_url'
    ];

    // Submission types
    const TYPE_FILE = 'file';
    const TYPE_IMAGE = 'image';
    const TYPE_LINK = 'link';

    /**
     * Get the task that this submission belongs to
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Get the user who made this submission
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this is a file submission
     */
    public function isFile(): bool
    {
        return $this->type === self::TYPE_FILE;
    }

    /**
     * Check if this is an image submission
     */
    public function isImage(): bool
    {
        return $this->type === self::TYPE_IMAGE;
    }

    /**
     * Check if this is a link submission
     */
    public function isLink(): bool
    {
        return $this->type === self::TYPE_LINK;
    }

    /**
     * Get the full URL for the uploaded file
     */
    public function getFileUrlAttribute(): ?string
    {
        if ($this->file_path) {
            return Storage::url($this->file_path);
        }
        return null;
    }

    /**
     * Get human readable file size
     */
    public function getFormattedFileSizeAttribute(): ?string
    {
        if (!$this->file_size) {
            return null;
        }

        $bytes = (int) $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get the icon class for the submission type
     */
    public function getIconAttribute(): string
    {
        switch ($this->type) {
            case self::TYPE_FILE:
                return 'bi-file-earmark';
            case self::TYPE_IMAGE:
                return 'bi-image';
            case self::TYPE_LINK:
                return 'bi-link-45deg';
            default:
                return 'bi-file';
        }
    }

    /**
     * Get the color class for the submission type
     */
    public function getColorAttribute(): string
    {
        switch ($this->type) {
            case self::TYPE_FILE:
                return 'primary';
            case self::TYPE_IMAGE:
                return 'success';
            case self::TYPE_LINK:
                return 'info';
            default:
                return 'secondary';
        }
    }

    /**
     * Get a display-friendly file name without HTML tags
     */
    public function getDisplayFilenameAttribute(): ?string
    {
        if (!$this->original_filename) {
            return null;
        }

        return $this->original_filename;
    }

    /**
     * Get file extension in uppercase
     */
    public function getFileExtensionAttribute(): ?string
    {
        if (!$this->original_filename) {
            return null;
        }
        
        $extension = pathinfo($this->original_filename, PATHINFO_EXTENSION);
        return $extension ? strtoupper($extension) : null;
    }

    /**
     * Check if this is an image file based on mime type
     */
    public function isImageFile(): bool
    {
        if (!$this->mime_type) {
            return false;
        }
        
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Get a preview-ready filename for display in forms and lists
     */
    public function getPreviewNameAttribute(): string
    {
        if ($this->type === self::TYPE_LINK) {
            return parse_url($this->link_url, PHP_URL_HOST) ?? 'External Link';
        }
        
        if ($this->original_filename) {
            $maxLength = 50;
            if (strlen($this->original_filename) > $maxLength) {
                $extension = pathinfo($this->original_filename, PATHINFO_EXTENSION);
                $basename = pathinfo($this->original_filename, PATHINFO_FILENAME);
                $truncated = substr($basename, 0, $maxLength - strlen($extension) - 4);
                return $truncated . '...' . ($extension ? ".{$extension}" : '');
            }
            return $this->original_filename;
        }
        
        return $this->title ?: 'Untitled Submission';
    }
}
