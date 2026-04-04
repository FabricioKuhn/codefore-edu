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
        // Apaga aulas agendadas existentes para não duplicar se o professor editar a turma
        $this->lessons()->where('status', 'scheduled')->delete();

        $lessonsCreated = 0;
        $currentDate = Carbon::parse($this->start_date);
        
        // Lista básica de feriados fixos (Exemplo Brasil)
        $holidays = [
            '01-01', '21-04', '01-05', '07-09', '12-10', '02-11', '15-11', '25-12'
        ];

        while ($lessonsCreated < $this->total_lessons) {
            // Verifica se o dia da semana atual está nos dias escolhidos (0=Dom, 1=Seg...)
            // Nota: O cast (string) garante compatibilidade com o JSON do banco
            if (in_array((string)$currentDate->dayOfWeek, $this->days_of_week)) {
                
                $isHoliday = in_array($currentDate->format('d-m'), $holidays);

                // Se não for feriado OU se o professor NÃO marcou para pular feriados
                if (!$isHoliday || !$this->skip_holidays) {
                    $this->lessons()->create([
                        'title' => 'Aula Agendada',
                        'date' => $currentDate->format('Y-m-d'),
                        'start_time' => $this->start_time,
                        'end_time' => $this->end_time,
                        'status' => 'scheduled',
                    ]);
                    $lessonsCreated++;
                }
            }
            
            $currentDate->addDay();

            // Segurança para não entrar em loop infinito se não houver dias selecionados
            if ($currentDate->diffInYears($this->start_date) > 2) break;
        }
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
}
