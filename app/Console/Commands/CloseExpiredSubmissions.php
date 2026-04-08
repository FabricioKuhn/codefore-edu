<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Submission;
use Carbon\Carbon;

class CloseExpiredSubmissions extends Command
{
    // O nome do comando que usaremos no terminal
    protected $signature = 'app:close-expired-submissions';

    // A descrição do que ele faz
    protected $description = 'Verifica e fecha automaticamente submissões de alunos cujo tempo esgotou.';

    public function handle()
    {
        // Pega todas as submissões que ainda estão "em andamento"
        $submissions = Submission::whereNull('finished_at')
            ->with('activity') // Carrega a atividade para saber o tempo limite
            ->get();

        $closedCount = 0;

        foreach ($submissions as $submission) {
            $activity = $submission->activity;
            $isExpired = false;

            // Verifica se a atividade tem um tempo limite em minutos
            if ($activity->time_limit_minutes) {
                // Calcula a hora exata que deveria ter acabado
                $expiresAt = $submission->started_at->copy()->addMinutes($activity->time_limit_minutes);
                
                // Se a hora atual for maior que a hora que deveria expirar...
                if (now()->greaterThanOrEqualTo($expiresAt)) {
                    $isExpired = true;
                }
            }

            // Opcional: Se você tiver um campo "deadline" ou "end_date" na Activity
            // if ($activity->end_date && now()->greaterThan($activity->end_date)) {
            //     $isExpired = true;
            // }

            if ($isExpired) {
                // Verifica se a prova tinha questões discursivas para definir o status final
                $needsManualGrading = $activity->questions()->where('type', '!=', 'multiple_choice')->exists();

                $submission->update([
                    'finished_at' => $expiresAt ?? now(),
                    // Se precisar de correção, vai pra "pending_review", senão "completed"
                    'status' => $needsManualGrading ? 'pending_review' : 'completed',
                ]);

                $closedCount++;
            }
        }

        $this->info("Rotina concluída: {$closedCount} submissões foram fechadas por tempo esgotado.");
    }
}