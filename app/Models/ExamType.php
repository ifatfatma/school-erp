<?php

namespace App\Models;

use App\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamType extends Model
{
    use SoftDeletes, BelongsToSchool;

    protected $fillable = [

        'school_id',

        'name',

        'code',

        'description',

        'status',

        'created_by',

        'updated_by',

    ];

    protected $casts = [

        'status' => 'boolean',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class,'updated_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when(
                $filters['search'] ?? null,
                function($q, $value) {
                    $q->where(function($sq) use ($value) {
                        $sq->where('name', 'like', "%{$value}%")
                           ->orWhere('code', 'like', "%{$value}%");
                    });
                }
            )
            ->when(
                isset($filters['status']) && $filters['status'] !== '',
                fn($q) => $q->where('status', (bool) $filters['status'])
            );
    }
}