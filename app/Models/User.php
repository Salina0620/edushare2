<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasApiTokens;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * Add is_admin if you want to mass-assign it from Filament/UserResource.
     * If you prefer to set it only manually in tinker, you can remove it here.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin', // optional convenience
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * Accessors appended to array / JSON.
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Casts.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean', // important for Filament gate
        ];
    }

    /**
     * Filament: can this user access a given panel?
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Only allow admins into /admin panel
        return (bool) $this->is_admin;
    }

    /**
     * Filament: display name in the topbar.
     */
    public function getFilamentName(): string
    {
        return $this->name ?? $this->email;
    }

    // (Optional) relationships for convenience
    // public function notes()
    // {
    //     return $this->hasMany(Note::class);
    // }


    public function likes()
    {
        return $this->hasMany(\App\Models\Like::class);
    }
    public function bookmarks()
    {
        return $this->hasMany(\App\Models\Bookmark::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_user')->withTimestamps()->withPivot('role');
    }

    public function createdGroups()
    {
        return $this->hasMany(Group::class, 'created_by');
    }
}
