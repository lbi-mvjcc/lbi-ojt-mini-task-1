<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Role constants
    const ROLE_ADMIN = 'admin';
    const ROLE_CUSTOMER = 'customer';
    const ROLE_FRONTEND_DEV = 'frontend_dev';
    const ROLE_BACKEND_DEV = 'backend_dev';
    const ROLE_SERVER_ADMIN = 'server_admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_picture',
        'bio',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'customer_id');
    }

    public function tasksCreated()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function tasksAssigned()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function sentNotifications()
    {
        return $this->hasMany(Notification::class, 'from_user_id');
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is a customer
     */
    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    /**
     * Check if user is a frontend developer
     */
    public function isFrontendDeveloper(): bool
    {
        return $this->role === self::ROLE_FRONTEND_DEV;
    }

    /**
     * Check if user is a backend developer
     */
    public function isBackendDeveloper(): bool
    {
        return $this->role === self::ROLE_BACKEND_DEV;
    }

    /**
     * Check if user is a server admin
     */
    public function isServerAdmin(): bool
    {
        return $this->role === self::ROLE_SERVER_ADMIN;
    }

    /**
     * Check if user is a developer (any type)
     */
    public function isDeveloper(): bool
    {
        return in_array($this->role, [
            self::ROLE_FRONTEND_DEV,
            self::ROLE_BACKEND_DEV,
            self::ROLE_SERVER_ADMIN,
        ]);
    }

    /**
     * Get role label in human-readable format
     */
    public function getRoleLabel(): string
    {
        $labels = [
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_CUSTOMER => 'Customer',
            self::ROLE_FRONTEND_DEV => 'Frontend Developer',
            self::ROLE_BACKEND_DEV => 'Backend Developer',
            self::ROLE_SERVER_ADMIN => 'Server Admin',
        ];

        return $labels[$this->role] ?? ucfirst(str_replace('_', ' ', $this->role));
    }

    /**
     * Get profile picture URL or default avatar
     */
    public function getProfilePictureUrl(): string
    {
        if ($this->profile_picture && file_exists(public_path('storage/' . $this->profile_picture))) {
            return asset('storage/' . $this->profile_picture);
        }
        
        // Return default avatar URL or placeholder
        return asset('assets/default-avatar.png');
    }

    /**
     * Get profile picture HTML
     */
    public function getProfilePictureHtml(int $size = 32): string
    {
        if ($this->profile_picture && file_exists(public_path('storage/' . $this->profile_picture))) {
            return '<img src="' . asset('storage/' . $this->profile_picture) . '" alt="' . $this->name . '" style="width: ' . $size . 'px; height: ' . $size . 'px; border-radius: 50%; object-fit: cover;">';
        }
        
        return '<i class="bi bi-person-circle" style="font-size: ' . ($size * 0.8) . 'px;"></i>';
    }
}
