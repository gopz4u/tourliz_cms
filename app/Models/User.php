<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_banned',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Role Constants
     */
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_EMPLOYEE = 'employee';
    const ROLE_AGENCY = 'agency';
    const ROLE_USER = 'user';

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_banned' => 'boolean',
    ];
    /**
     * Get the agency profile associated with the user.
     */
    public function agencyProfile()
    {
        return $this->hasOne(AgencyProfile::class);
    }

    /**
     * Check if user is an agency
     */
    public function isAgency()
    {
        return $this->role === self::ROLE_AGENCY;
    }

    /**
     * Check if user is an admin (Super Admin or Employee)
     */
    public function isAdmin()
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_EMPLOYEE, 'admin']);
    }

    /**
     * Check if user is a Super Admin
     */
    public function isSuperAdmin()
    {
        return $this->role === self::ROLE_SUPER_ADMIN || $this->role === 'admin';
    }

    /**
     * Check if user is an Employee
     */
    public function isEmployee()
    {
        return $this->role === self::ROLE_EMPLOYEE;
    }
}
