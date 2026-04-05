<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EventCategories;
use App\Models\Accounts;
use App\Models\TicketType;

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
        'category_id',
        'organizer_id',
        'title',
        'description',
        'banner',
        'location',
        'city',
        'format',
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

    /**
     * Get the category that owns the event.
     */
    public function category()
    {
        return $this->belongsTo(EventCategories::class, 'category_id', 'id_category');
    }

    /**
     * Get the organizer that manages the event.
     */
    public function organizer()
    {
        return $this->belongsTo(Accounts::class, 'organizer_id', 'id');
    }

    /**
     * Get the ticket types for this event.
     */
    public function ticketTypes()
    {
        return $this->belongsToMany(TicketType::class, 'event_ticket_types', 'event_id', 'ticket_type_id')
                    ->withPivot('id', 'price', 'stock')
                    ->withTimestamps();
    }
}
