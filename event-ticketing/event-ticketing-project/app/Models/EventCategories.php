<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventCategories extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'event_categories';

    /**
     * The primary key for the table.
     */
    protected $primaryKey = 'id_category';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Get the events for the category.
     */
    public function events()
    {
        return $this->hasMany(Events::class, 'category_id', 'id_category');
    }
}
