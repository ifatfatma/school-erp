<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $table = 'plans';

    protected $fillable = [
        'name',
        'slug',
        'price',
        'max_schools',
        'features',
        'description',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_schools' => 'integer',
        'features' => 'array',
        'status' => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
