<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChecklistItem extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'is_required',
        'is_active',
        'order',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who created this checklist item.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all student checklists using this item.
     */
    public function studentChecklists()
    {
        return $this->hasMany(StudentChecklist::class);
    }

    /**
     * Get all documents for this checklist item.
     */
    public function documents()
    {
        return $this->hasMany(StudentDocument::class);
    }

    /**
     * Get the programs this checklist item belongs to.
     */
    public function programs()
    {
        return $this->belongsToMany(Program::class, 'checklist_program')
            ->orderBy('checklist_items.order');
    }

    /**
     * Scope a query to only include active checklist items.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by the order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get the name attribute (alias for title).
     */
    public function getNameAttribute()
    {
        return $this->title;
    }
}
