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
        'team_id',
        'title',
        'content',
        'slug',
        'share_token',
        'is_public',
        'tags',
        'status',
        'folder',
        'starred',
        'last_saved_at',
    ];

    protected $casts = [
        'last_saved_at' => 'datetime',
        'starred'       => 'boolean',
        'is_public'     => 'boolean',
        'tags'          => 'array',
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

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class)->orderByDesc('version_number');
    }

    public function saveVersion(): void
    {
        $last = $this->versions()->first();
        // Skip if content hasn't changed since last version
        if ($last && $last->content === $this->content && $last->title === $this->title) {
            return;
        }
        $nextNumber = ($last?->version_number ?? 0) + 1;
        // Keep max 30 versions per document
        if ($this->versions()->count() >= 30) {
            $this->versions()->orderBy('version_number')->first()->delete();
        }
        $this->versions()->create([
            'title'          => $this->title,
            'content'        => $this->content,
            'version_number' => $nextNumber,
        ]);
    }
}
