<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'event';

    /**
     * The primary key for the table.
     */
    protected $primaryKey = 'id_event';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'description',
        'banner',
        'location',
        'date',
        'status',
    ];

    /**
     * Cast attributes to proper types.
     */
    protected function casts(): array
    {
        return [
            'date' => 'datetime',
        ];
    }

    // ─────────────────────────────────────────────
    // Helper Methods
    // ─────────────────────────────────────────────

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
