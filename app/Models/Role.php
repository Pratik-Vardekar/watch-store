<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions',
        'is_system',
        'is_active',
        'icon',
        'color',
        'sort_order',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get all users with this role
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if role is system role
     */
    public function isSystem()
    {
        return $this->is_system;
    }

    /**
     * Check if role is active
     */
    public function isActive()
    {
        return $this->is_active;
    }

    /**
     * Get role color class
     */
    public function getColorClassAttribute()
    {
        return $this->color ?? 'secondary';
    }

    /**
     * Get role icon class
     */
    public function getIconClassAttribute()
    {
        return $this->icon ?? 'ti-user';
    }

    /**
     * Scope for active roles
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for system roles
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope for non-system roles
     */
    public function scopeCustom($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * Check if role has specific permission
     */
    public function hasPermission($permission)
    {
        if (!$this->permissions) {
            return false;
        }

        return in_array($permission, $this->permissions) ?? false;
    }

    /**
     * Check if role has any of the given permissions
     */
    public function hasAnyPermission($permissions)
    {
        if (!$this->permissions) {
            return false;
        }

        return !empty(array_intersect($permissions, $this->permissions));
    }
}