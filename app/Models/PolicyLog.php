<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyLog extends Model
{
    use HasFactory;

    protected $table = 'policy_log';

    protected $guarded = [];

    protected $casts = [
        'reasons' => 'array',
        'context' => 'array',
    ];
}
