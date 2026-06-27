<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'slug',
        'status',
        'folder',
        'starred',
        'last_saved_at',
    ];

    protected $casts = [
        'last_saved_at' => 'datetime',
        'starred'       => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Document $document) {
            if (empty($document->slug)) {
                $document->slug = Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
