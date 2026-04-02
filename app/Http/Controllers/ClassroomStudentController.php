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
        abort_if($classroom->teacher_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $username = Str::slug($validated['name']) . rand(100, 999);

        // Ensure unique username
        while (User::where('username', $username)->exists()) {
            $username = Str::slug($validated['name']) . rand(100, 999);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => $username,
            'password' => Hash::make($validated['password']),
            'role' => 'student',
            'institution_id' => $request->user()->institution_id,
        ]);

        $classroom->students()->attach($user->id);

        return redirect()->back()->with('success', 'Aluno cadastrado e matriculado com sucesso!');
    }
}
