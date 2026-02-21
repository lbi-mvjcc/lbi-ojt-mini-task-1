<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title',
        'description',
        'link',
        'category',
        'status',
        'project_id',
        'customer_id',
        'assigned_to',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function assignedDeveloper(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    public function autoAssign(): void
    {
        $developer = match($this->category) {
            'frontend' => $this->project->frontendDeveloper(),
            'backend' => $this->project->backendDeveloper(),
            'server' => $this->project->serverAdmin(),
            default => null,
        };

        if ($developer) {
            $this->assigned_to = $developer->id;
            $this->save();
        }
    }
}
