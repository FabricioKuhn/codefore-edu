<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'coin_conversion_rate' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'disabled_students' => 'array',
    ];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
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
