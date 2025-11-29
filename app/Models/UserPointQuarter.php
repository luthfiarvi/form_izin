<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPointQuarter extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'year',
        'quarter',
        'starting_points',
        'ending_points',
        'total_deduction',
        'closed_at',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
