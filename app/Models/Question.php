<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Question extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'institution_id', 'user_id', 'type', 'statement', 
        'guidelines', 'external_link', 'external_link_label', 
        'attachments', 'expected_answer', 'default_weight', 
        'status', 'tags'
    ];

    protected $casts = [
        'attachments' => 'array',
        'status' => 'boolean',
        'tags' => 'array',
    ];

    // 🌟 AGORA PERTENCE À INSTITUIÇÃO (Banco de Questões)
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    // 🌟 NOVA RELAÇÃO: Uma questão pode estar em várias tarefas/provas
    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'activity_question')
                    ->withPivot('weight_override')
                    ->withTimestamps();
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
    }
}