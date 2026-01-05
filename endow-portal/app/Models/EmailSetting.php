<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'mailer',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_address',
        'from_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'port' => 'integer',
    ];

    /**
     * Get decrypted password
     */
    public function getDecryptedPassword()
    {
        try {
            return $this->password ? decrypt($this->password) : '';
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Boot method to ensure only one settings record
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Delete all existing records before creating new one
            static::query()->delete();
        });
    }
}
