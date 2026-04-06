<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClassroomStudentController extends Controller
{
    public function store(Request $request, Classroom $classroom)
{
    // Valida apenas o ID do aluno selecionado
    $request->validate([
        'student_id' => 'required|exists:users,id',
    ]);

    // Faz o vínculo na tabela pivot
    $classroom->students()->syncWithoutDetaching([$request->student_id]);

    return back()->with('success', 'Aluno vinculado com sucesso!');
}
}
