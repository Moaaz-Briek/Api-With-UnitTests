<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Travel extends Model
{
    use HasFactory, Sluggable, HasUuids;

    protected $table = 'travels';

    protected $fillable = [
        'name',
        'description',
        'is_public',
        'slug',
        'number_of_days',
    ];

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    }

    public function sluggable(): array
    {
        // TODO: Implement sluggable() method.
        return [
            'slug' => [
                'source' => 'name'
                ]
            ];
    }

    /**
     * @return Attribute
     */
    public function numberOfNights(): Attribute
    {
        return Attribute::make(
          get: fn ($value, $attributes) => $attributes['number_of_days'] - 1
        );
    }
}