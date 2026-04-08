<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonMaterial extends Model
{
    protected $fillable = ['lesson_id', 'type', 'title', 'path_or_url'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
