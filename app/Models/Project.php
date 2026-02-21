<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function frontendDeveloper()
    {
        return $this->members()->wherePivot('role', 'frontend_developer')->first();
    }

    public function backendDeveloper()
    {
        return $this->members()->wherePivot('role', 'backend_developer')->first();
    }

    public function serverAdmin()
    {
        return $this->members()->wherePivot('role', 'server_admin')->first();
    }
}
