<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Events Model
 *
 * Represents a ticketed event with category, organizer, and ticket types.
 * Supports statuses: draft, published, cancelled, completed.
 *
 * Used by: EventController, UserController, OrganizerController, AdminController.
 */
class Events extends Model
{
    use HasFactory;

    protected $table      = 'event';
    protected $primaryKey = 'id_event';

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

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
        ];
    }

    // ─── Accessors ───────────────────────────────────────

    /**
     * Get the full URL for the banner image (handles both local and external URLs).
     */
    public function getBannerUrlAttribute(): ?string
    {
        if (empty($this->banner)) {
            return null;
        }

        if (str_starts_with($this->banner, 'http://') || str_starts_with($this->banner, 'https://')) {
            return $this->banner;
        }

        return asset('storage/' . $this->banner);
    }

    // ─── Status Helpers ──────────────────────────────────

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    // ─── Relationships ───────────────────────────────────

    /** Event category (e.g. Music, Festival, Workshop) */
    public function category()
    {
        return $this->belongsTo(EventCategories::class, 'category_id', 'id_category');
    }

    /** The organizer managing this event */
    public function organizer()
    {
        return $this->belongsTo(Accounts::class, 'organizer_id', 'id');
    }

    /** Ticket types with per-event price and stock (many-to-many pivot) */
    public function ticketTypes()
    {
        return $this->belongsToMany(TicketType::class, 'event_ticket_types', 'event_id', 'ticket_type_id')
            ->withPivot('id', 'price', 'stock')
            ->withTimestamps();
    }

    /** Vouchers scoped to this event */
    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'event_id', 'id_event');
    }
}
