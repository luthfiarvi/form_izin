<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'signature_path',
        'profile_photo_path',
        'is_kepala_kepegawaian',
        'status',
        'whatsapp_phone',
        'role',
        'points',
        'discipline_score',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'points' => 'integer',
            'discipline_score' => 'integer',
        ];
    }

    /**
     * Relasi: User memiliki banyak FormIzin
     */
    public function formIzins()
    {
        return $this->hasMany(\App\Models\FormIzin::class, 'user_id');
    }

    /**
     * Relasi: User sebagai pengambil keputusan FormIzin (decided_by)
     */
    public function decidedFormIzins()
    {
        return $this->hasMany(\App\Models\FormIzin::class, 'decided_by');
    }

    /**
     * Virtual accessor untuk status kedisiplinan (gamification badge).
     *
     * @return array{label:string,color:string}
     */
    public function getDisciplineStatusAttribute(): array
    {
        $score = (int) ($this->discipline_score ?? 100);

        if ($score >= 80) {
            return [
                'label' => 'Zona Aman (Role Model)',
                'color' => 'success',
            ];
        }

        if ($score >= 51) {
            return [
                'label' => 'Zona Waspada',
                'color' => 'warning',
            ];
        }

        return [
            'label' => 'Zona Bahaya (SP)',
            'color' => 'danger',
        ];
    }

    /**
     * Route notifications for Twilio WhatsApp channel.
     */
    public function routeNotificationForTwilio($notification): ?string
    {
        if (!empty($this->whatsapp_phone)) {
            $phone = (string) $this->whatsapp_phone;
            if (str_starts_with($phone, 'whatsapp:')) {
                return $phone;
            }
            return 'whatsapp:'.$phone;
        }
        return null;
    }
}
