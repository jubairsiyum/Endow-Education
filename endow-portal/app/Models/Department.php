<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'manager_id',
        'icon',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the manager of the department
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get all users in this department (many-to-many)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'department_user')
                    ->withTimestamps();
    }

    /**
     * Get users with department_id set to this department (legacy)
     */
    public function usersLegacy()
    {
        return $this->hasMany(User::class, 'department_id');
    }

    /**
     * Get daily reports for this department
     */
    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class, 'department_id');
    }

    /**
     * Scope to filter active departments
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by manager
     */
    public function scopeByManager($query, $managerId)
    {
        return $query->where('manager_id', $managerId);
    }
}
