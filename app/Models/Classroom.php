<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classroom extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'level_growth_factor' => 'decimal:2',
        'days_of_week' => 'array',
    'start_date' => 'date',
    ];

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
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

    public function generateLessons()
{
    // Limpa aulas futuras se for uma re-geração (opcional, cuidado aqui)
    // $this->lessons()->where('status', 'scheduled')->delete();

    $currentDate = \Carbon\Carbon::parse($this->start_date);
    $lessonsCreated = 0;
    $days = $this->days_of_week; // Ex: [1, 4] para Segunda e Quinta

    while ($lessonsCreated < $this->total_lessons) {
        // Se a frequência for semanal/quinzenal, verifica se o dia atual é um dos escolhidos
        if (in_array($currentDate->dayOfWeek, $days)) {
            
            // Aqui entraria a lógica de pular feriados futuramente
            // if ($this->skip_holidays && isHoliday($currentDate)) { $currentDate->addDay(); continue; }

            $this->lessons()->create([
                'date' => $currentDate->format('Y-m-d'),
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'status' => 'scheduled'
            ]);

            $lessonsCreated++;
        }

        // Lógica de avanço da data
        if ($this->frequency === 'daily') {
            $currentDate->addDay();
        } elseif ($this->frequency === 'biweekly' && $currentDate->dayOfWeek == end($days)) {
             // Se for quinzenal e chegamos no último dia de aula da semana, pula 1 semana extra
             $currentDate->addWeeks(1)->startOfWeek()->addDays(reset($days)); 
        } else {
            $currentDate->addDay();
        }
        
        // Trava de segurança para não entrar em loop infinito
        if ($lessonsCreated > 500) break; 
    }
}

}
