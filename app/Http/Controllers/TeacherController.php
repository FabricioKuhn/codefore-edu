<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = User::where('role', 'teacher')
            ->where('institution_id', auth()->user()->institution_id)
            ->withCount('taughtClassrooms')
            ->get();

        return view('teachers.index', compact('teachers'));
    }

    public function create()
    {
        return redirect()->route(auth()->user()->role . '.teachers.index')->with('open_modal', true);
    }

    public function store(Request $request)
{
    // 1. Validamos os dados e os colocamos na variável $validated
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'cpf' => ['nullable', 'string', 'max:20', new \App\Rules\CpfValido],
        'birth_date' => 'nullable|date',
        'phone' => 'nullable|string|max:20',
        'zip_code' => 'nullable|string|max:20',
        'street' => 'nullable|string|max:255',
        'number' => 'nullable|string|max:50',
        'neighborhood' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'state' => 'nullable|string|max:2',
        'complement' => 'nullable|string|max:255',
    ]);

    // 2. Tratamos o upload do avatar se ele existir
    if ($request->hasFile('avatar')) {
        $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
    }

    // 3. Preparamos os campos automáticos
    $validated['password'] = Hash::make($request->password);
    $validated['role'] = 'teacher';
    $validated['institution_id'] = auth()->user()->institution_id;
    $validated['is_active'] = true;
    
    // Geramos um username único caso o banco exija
    $validated['username'] = strstr($request->email, '@', true) . rand(100, 999);

    // 4. Criamos o usuário passando o array $validated completo
    // Certifique-se que esses campos (cpf, phone, etc) estejam no $fillable do seu Model User
    User::create($validated);

    return redirect()->route(auth()->user()->role . '.teachers.index')
        ->with('success', 'Professor cadastrado com sucesso!');
}

    public function edit(User $teacher)
    {
        return redirect()->route(auth()->user()->role . '.teachers.index')->with('edit_teacher_id', $teacher->id);
    }

    public function update(Request $request, User $teacher)
    {
        if ($teacher->institution_id !== auth()->user()->institution_id) {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$teacher->id],
        ]);

        $teacher->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $teacher->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route(auth()->user()->role . '.teachers.index')->with('success', 'Dados do professor atualizados!');
    }

    public function destroy(User $teacher)
    {
        if ($teacher->institution_id !== auth()->user()->institution_id) {
            abort(403);
        }

        // Verifica se o professor tem turmas vinculadas antes de excluir
        if ($teacher->taughtClassrooms()->exists()) {
            return redirect()->route(auth()->user()->role . '.teachers.index')->with('error', 'Não é possível excluir um professor com turmas vinculadas. Remova o vínculo primeiro.');
        }

        $teacher->delete();

        return redirect()->route(auth()->user()->role . '.teachers.index')->with('success', 'Professor removido com sucesso!');
    }

    public function toggleStatus(\App\Models\User $teacher)
    {
        // Inverte o status atual (se for true vira false, se for false vira true)
        $teacher->update(['is_active' => !$teacher->is_active]);

        return back()->with('success', 'Status do professor atualizado com sucesso!');
    }
}
