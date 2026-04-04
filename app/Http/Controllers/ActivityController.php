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

    public function create(Request $request)
    {
        $classroom = Classroom::findOrFail($request->classroom_id);
        if ($classroom->teacher_id !== request()->user()->id) {
            abort(403);
        }
        return view('activities.create', compact('classroom'));
    }

    public function store(Request $request)
    {
        $classroom = Classroom::findOrFail($request->classroom_id);
        if ($classroom->teacher_id !== auth()->id()) {
            abort(403);
        }

        if ($request->status === 'active' && !auth()->user()->institution->canCreate('active_activities')) { 
            return back()->with('error', 'Limite de atividades ativas atingido.'); 
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_xp' => 'required|integer|min:1',
            'coin_conversion_rate' => 'required|numeric|min:0|max:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'duration_minutes' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,active,closed,canceled',
            'shuffle_options' => 'nullable|boolean',
        ]);

        $classroom->activities()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'base_xp' => $validated['base_xp'],
            'coin_conversion_rate' => $validated['coin_conversion_rate'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'duration_minutes' => $validated['duration_minutes'],
            'status' => $validated['status'],
            'shuffle_options' => $request->boolean('shuffle_options'),
        ]);

        return redirect()->route('classrooms.show', $classroom)
                         ->with('success', 'Atividade salva com sucesso!');
    }

    public function update(Request $request, Activity $activity)
    {
        if ($activity->classroom->teacher_id !== auth()->id()) {
            abort(403);
        }

        if ($request->status === 'active' && !auth()->user()->institution->canCreate('active_activities')) { 
            return back()->with('error', 'Limite de atividades ativas atingido.'); 
        }

        $validated = $request->validate([
            'status' => 'sometimes|in:draft,active,closed,canceled',
            'shuffle_options' => 'sometimes|boolean',
        ]);

        if ($request->has('shuffle_options')) {
            $activity->shuffle_options = $request->boolean('shuffle_options');
        } elseif ($request->has('status') && !$request->has('shuffle_options') && $request->method() !== 'PATCH') {
            // Se o form envia só status sem shuffle via submit nativo (sem js ajax partial update), 
            // the checkbox has un-checked itself technically if it was absent, but our frontend toggle submits via form that has hidden status so let's only update what's present if we do a focused push.
            // A safer approach based on the UI provided is just updating what was sent.
            // In the UI, the toggle form sends: shuffle_options=1 (if checked) + status. But if unchecked, it sends *only* status!
            // Wait, the status select and shuffle toggle are different forms!
        }

        // To handle different forms, we check if they are explicitly present. If the shuffle_options form is submitted, it has 'shuffle_options' or it doesn't. But checkbox omitted = false only if that specific form was submitted.
        // Looking at the view: the shuffle form has a hidden `status` input. So if `status` is sent, we update status.
        if ($request->has('status')) {
            $activity->status = $request->input('status');
        }

        // Se o toggle de shuffle foi na requisição, ele é 1 or missing. But the status form also misses it! We need a hidden field to identify the form.
        if ($request->has('shuffle_update')) { // we will add this hidden field to the view form
            $activity->shuffle_options = $request->boolean('shuffle_options');
        }

        if ($request->has('start_date')) {
            $activity->start_date = $request->input('start_date') ?: null;
        }

        if ($request->has('end_date')) {
            $activity->end_date = $request->input('end_date') ?: null;
        }

        if ($request->has('duration_minutes')) {
            $activity->duration_minutes = $request->input('duration_minutes') ?: null;
        }

        $activity->save();

        return back()->with('success', 'Configurações atualizadas!');
    }

    public function toggleStudent(Activity $activity, $studentId)
    {
        if ($activity->classroom->teacher_id !== request()->user()->id) {
            abort(403);
        }

        $disabledStudents = is_array($activity->disabled_students) 
            ? $activity->disabled_students 
            : json_decode($activity->disabled_students, true) ?? [];

        if (in_array($studentId, $disabledStudents)) {
            // Remove
            $disabledStudents = array_diff($disabledStudents, [$studentId]);
        } else {
            // Add
            $disabledStudents[] = $studentId;
        }

        $activity->disabled_students = array_values($disabledStudents); // re-index
        $activity->save();

        return back()->with('success', 'Status do aluno na atividade atualizado!');
    }
}
