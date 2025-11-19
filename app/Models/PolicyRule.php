<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PolicyRule extends Model
{
    use HasFactory;

    protected $table = 'policy_rules';

    protected $guarded = [];
}

