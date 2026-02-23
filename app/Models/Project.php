<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'customer_id',
        'frontend_developer_id',
        'backend_developer_id',
        'server_admin_id',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // Team member relationships
    public function frontendDeveloper()
    {
        return $this->belongsTo(User::class, 'frontend_developer_id');
    }

    public function backendDeveloper()
    {
        return $this->belongsTo(User::class, 'backend_developer_id');
    }

    public function serverAdmin()
    {
        return $this->belongsTo(User::class, 'server_admin_id');
    }

    /**
     * Check if project has a complete team (1 FE, 1 BE, 1 SA)
     */
    public function hasCompleteTeam(): bool
    {
        return $this->frontend_developer_id !== null 
            && $this->backend_developer_id !== null 
            && $this->server_admin_id !== null;
    }

    /**
     * Get the developer for a specific category
     */
    public function getDeveloperForCategory(string $category): ?User
    {
        return match($category) {
            'frontend' => $this->frontendDeveloper,
            'backend' => $this->backendDeveloper,
            'server' => $this->serverAdmin,
            default => null,
        };
    }
}
