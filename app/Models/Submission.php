<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_enabled' => 'boolean',
        'custom_deadline' => 'datetime',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'answers' => 'array',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SubmissionAnswer::class);
    }
}