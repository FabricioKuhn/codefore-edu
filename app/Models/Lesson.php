<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Garante que o Laravel trate a data como um objeto Carbon
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Relacionamento: Uma aula possui várias chamadas (uma por aluno)
     */
    public function attendances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Relacionamento: Uma aula pertence a uma turma
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function getStatusLabelAttribute()
{
    return match ($this->status) {
        'scheduled' => 'Agendada',
        'canceled' => 'Cancelada',
        'recorded' => 'Registrada',
        default => 'Não Iniciada',
    };
}

public function materials()
{
    return $this->hasMany(LessonMaterial::class);
}

public function activities()
{
    return $this->hasMany(Activity::class);
}

public function studentsCompleted()
{
    return $this->belongsToMany(User::class, 'lesson_student')->withPivot('completed_at')->withTimestamps();
}
}