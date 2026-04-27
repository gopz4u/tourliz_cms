<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'admins';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Role Constants
     */
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_EMPLOYEE = 'employee';

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
        return $this->role === self::ROLE_SUPER_ADMIN || $this->role === 'admin' || empty($this->role);
    }

    /**
     * Check if user is an Employee
     */
    public function isEmployee()
    {
        return $this->role === self::ROLE_EMPLOYEE;
    }
}

