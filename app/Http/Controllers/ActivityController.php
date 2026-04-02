<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Classroom;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function show(Activity $activity)
    {
        if ($activity->classroom->teacher_id !== request()->user()->id) {
            abort(403);
        }
        
        $activity->load('questions.options');
        return view('activities.show', compact('activity'));
    }

    public function create(Classroom $classroom)
    {
        if ($classroom->teacher_id !== request()->user()->id) {
            abort(403);
        }
        return view('activities.create', compact('classroom'));
    }

    public function store(Request $request, Classroom $classroom)
    {
        if ($classroom->teacher_id !== request()->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_xp' => 'required|integer|min:1',
            'coin_conversion_rate' => 'required|numeric|min:0|max:1',
        ]);

        $classroom->activities()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'base_xp' => $validated['base_xp'],
            'coin_conversion_rate' => $validated['coin_conversion_rate'],
            'status' => 'draft',
        ]);

        return redirect()->route('classrooms.show', $classroom)
                         ->with('success', 'Missão criada e salva como rascunho com sucesso!');
    }
}
