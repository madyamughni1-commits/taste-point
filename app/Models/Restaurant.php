<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'min_price',
        'max_price',
        'description',
        'opening_hours',
        'opening_days',
        'rating',
        'latitude',
        'longitude',
        'google_maps_link',
        'image',
        'status',
    ];

    protected $casts = [
        'min_price' => 'integer',
        'max_price' => 'integer',
        'rating' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the menus for the restaurant
     */
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    /**
     * Get formatted price range
     */
    public function getPriceRangeAttribute()
    {
        return 'Rp ' . number_format($this->min_price, 0, ',', '.') . ' - ' . number_format($this->max_price, 0, ',', '.');
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return 'https://via.placeholder.com/400x200?text=No+Image';
    }
}