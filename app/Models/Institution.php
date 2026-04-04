<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'settings' => 'array',
        'modules_enabled' => 'array',
        'status' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    public function studentStats(): HasMany
    {
        return $this->hasMany(StudentStat::class);
    }

    // app/Models/Institution.php

public function plan()
{
    return $this->belongsTo(Plan::class);
}

// app/Models/Institution.php

public function canCreate(string $resource): bool
{
    // Se por algum motivo não tiver plano, barramos por segurança
    if (!$this->plan) return false;

    switch ($resource) {
        case 'classes':
            $limit = $this->plan->limit_classes;
            if (is_null($limit)) return true; // Ilimitado
            return $this->classrooms()->count() < $limit;

        case 'students':
            $limit = $this->plan->limit_students_per_class; 
            if (is_null($limit)) return true;
            // Aqui a lógica pode variar: limite total ou por turma. 
            // Vamos supor que seja limite total da escola por enquanto:
            return $this->users()->where('role', 'student')->count() < $limit;

            case 'active_activities':
    $limit = $this->plan->limit_active_activities; // Certifique-se que este campo existe no seu banco/plano
    if (is_null($limit)) return true;

    // Contamos quantas atividades 'active' ou 'in_progress' existem na instituição
    $activeCount = Activity::whereHas('classroom', function($q) {
        $q->where('institution_id', $this->id);
    })->whereIn('status', ['active', 'in_progress'])->count();

    return $activeCount < $limit;

        default:
            return true;
    }
}
}
