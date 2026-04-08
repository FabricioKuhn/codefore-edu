<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'coin_conversion_rate' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'exam_settings' => 'array', // Transforma o JSON do banco em Array no PHP
    ];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'activity_question')
                    ->withPivot('weight_override')
                    ->withTimestamps();
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'draft' => 'Rascunho',
            'active' => 'Ativa',
            'in_progress' => 'Em Andamento',
            'closed' => 'Encerrada',
            'canceled' => 'Cancelada',
            default => $this->status,
        };
    }
}