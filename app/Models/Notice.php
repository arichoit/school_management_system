<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notice extends Model
{
    protected $fillable = [
        'title',
        'content',
        'category',
        'is_published_public',
        'target_role',
        'published_at',
        'created_by',
    ];

    protected $casts = [
        'is_published_public' => 'boolean',
        'published_at' => 'date',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
