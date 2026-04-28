<?php

namespace App\Jobs;

use App\Models\Classroom;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class GenerateClassroomLessons implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Criamos o Job recebendo a instância da Turma
     */
    public function __construct(protected Classroom $classroom) {}

    /**
     * A lógica pesada fica aqui dentro
     */
    public function handle(): void
    {
        // 1. Limpa apenas as aulas agendadas (status: scheduled) 
        // para permitir regerar o calendário se necessário
        $this->classroom->lessons()->where('status', 'scheduled')->delete();

        $lessonsCreated = 0;
        $currentDate = Carbon::parse($this->classroom->start_date);
        
        // Feriados fixos (como definido anteriormente)
        $holidays = [
            '01-01', '21-04', '01-05', '07-09', '12-10', '02-11', '15-11', '25-12'
        ];

        while ($lessonsCreated < $this->classroom->total_lessons) {
            // Verifica se o dia atual é um dos dias da semana escolhidos
            if (in_array((string)$currentDate->dayOfWeek, $this->classroom->days_of_week)) {
                
                $isHoliday = in_array($currentDate->format('d-m'), $holidays);

                // Se não for feriado OU se a turma NÃO pula feriados, cria a aula
                if (!$isHoliday || !$this->classroom->skip_holidays) {
                    $this->classroom->lessons()->create([
                        'title' => 'Aula Agendada',
                        'date' => $currentDate->format('Y-m-d'),
                        'start_time' => $this->classroom->start_time,
                        'end_time' => $this->classroom->end_time,
                        'status' => 'scheduled',
                        'is_active' => true, // Seguindo seu padrão de Soft Control
                    ]);
                    $lessonsCreated++;
                }
            }
            
            $currentDate->addDay();

            // Trava de segurança para não entrar em loop infinito
            if ($currentDate->diffInYears($this->classroom->start_date) > 2) break;
        }
    }
}