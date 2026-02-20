<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['name', 'description', 'customer_id'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function frontendDeveloper()
    {
        return $this->members()->where('role', 'frontend_developer')->first()?->user;
    }

    public function backendDeveloper()
    {
        return $this->members()->where('role', 'backend_developer')->first()?->user;
    }

    public function serverAdministrator()
    {
        return $this->members()->where('role', 'server_administrator')->first()?->user;
    }
}
