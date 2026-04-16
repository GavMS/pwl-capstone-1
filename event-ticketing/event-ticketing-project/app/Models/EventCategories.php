<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * EventCategories Model
 *
 * Master data for event categories (e.g. Music, Workshop, Festival).
 *
 * Used by: Admin\EventCategoryController (CRUD), EventController (create/edit forms), UserController (explore filter).
 */
class EventCategories extends Model
{
    use HasFactory;

    protected $table      = 'event_categories';
    protected $primaryKey = 'id_category';

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    // ─── Relationships ───────────────────────────────────

    /** Events in this category */
    public function events()
    {
        return $this->hasMany(Events::class, 'category_id', 'id_category');
    }
}
