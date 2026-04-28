<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use Carbon\CarbonPeriod;


class Classroom extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'level_growth_factor' => 'decimal:2',
        'days_of_week' => 'array',
        'start_date' => 'date',
        'skip_holidays' => 'boolean',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function generateLessons()
    {
        // Validação básica: se não tiver dias da semana, não faz nada
        if (empty($this->days_of_week) || !is_array($this->days_of_week)) {
            return; 
        }

        // Chama o Job em background para processar as aulas
        \App\Jobs\GenerateClassroomLessons::dispatch($this);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'classroom_student', 'classroom_id', 'student_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function toggleStatus()
{
    // Inverte o is_active que já existe na sua tabela classrooms
    $novoStatus = !$this->is_active;
    $this->update(['is_active' => $novoStatus]);

    // O update() do Eloquent em relacionamentos é em massa, 
    // então ele vai desligar todas as lessons e activities de uma vez.
    $this->lessons()->update(['is_active' => $novoStatus]);
    $this->activities()->update(['is_active' => $novoStatus]);

    return $novoStatus;
}
}
