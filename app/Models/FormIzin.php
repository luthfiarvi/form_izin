<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormIzin extends Model
{
    use HasFactory;

    protected $table = 'form_izin';

    protected $fillable = [
        'user_id',
        'date',
        'in_time',
        'out_time',
        'purpose',
        'izin_type',
        'attachment_path',
        'approved_at',
        'rejected_at',
        'decided_by',
    ];

    protected $casts = [
        'date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
