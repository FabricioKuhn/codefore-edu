<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Classroom;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        // Busca apenas alunos da mesma instituição do professor/admin logado
        $query = User::where('role', 'student')
                     ->where('institution_id', $request->user()->institution_id)
                     ->with('classrooms');

        // Filtro de busca (Nome ou E-mail)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(15);
        $classrooms = Classroom::where('institution_id', $request->user()->institution_id)->get();

        return view('students.index', compact('students', 'classrooms'));
    }

    public function toggleStatus(User $student)
    {
        $student->is_active = !$student->is_active;
        $student->save();

        return back()->with('success', 'Status do aluno atualizado com sucesso!');
    }

    public function enroll(Request $request, User $student)
    {
        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id'
        ]);

        // Vincula o aluno à turma sem duplicar se ele já estiver lá
        $student->classrooms()->syncWithoutDetaching([$request->classroom_id]);

        return back()->with('success', 'Aluno vinculado à turma com sucesso!');
    }

   public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'cpf' => ['nullable', 'string', 'max:20', new \App\Rules\CpfValido],
            'birth_date' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'zip_code' => 'nullable|string|max:20',
            'street' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:50',
            'neighborhood' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:2',
            'complement' => 'nullable|string|max:255',
            'attachments.*' => 'nullable|file|max:5120', // Máximo 5MB por arquivo
        ]);

        // Tratar Upload de Anexos
        $documents = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('student_documents', 'public');
                $documents[] = [
                    'original_name' => $file->getClientOriginalName(),
                    'url' => \Illuminate\Support\Facades\Storage::url($path),
                    'path' => $path
                ];
            }
        }

        // Criar o Usuário
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'role' => 'student',
            'institution_id' => $request->user()->institution_id,
            'username' => \Illuminate\Support\Str::slug($validated['name'], '.') . rand(100, 999),
            'cpf' => $validated['cpf'],
            'birth_date' => $validated['birth_date'],
            'phone' => $validated['phone'],
            'guardian_name' => $validated['guardian_name'],
            'guardian_phone' => $validated['guardian_phone'],
            'zip_code' => $validated['zip_code'],
            'street' => $validated['street'],
            'neighborhood' => $validated['neighborhood'],
            'city' => $validated['city'],
            'state' => $validated['state'],
            'complement' => $validated['complement'],
            'documents' => !empty($documents) ? $documents : null,
        ]);

        return redirect()->route('students.index')->with('success', 'Aluno cadastrado com sucesso!');
    }

    public function edit(User $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, User $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->id,
            'password' => 'nullable|min:8',
            'cpf' => ['nullable', 'string', 'max:20', new \App\Rules\CpfValido],
            'birth_date' => 'nullable|date',
            'phone' => 'nullable|string|max:20',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:20',
            'zip_code' => 'nullable|string|max:20',
            'street' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:50',
            'neighborhood' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:2',
            'complement' => 'nullable|string|max:255',
            'attachments.*' => 'nullable|file|max:5120',
        ]);

        // Lógica de Documentos (Adicionar novos aos existentes)
        $documents = $student->documents ?? [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('student_documents', 'public');
                $documents[] = [
                    'original_name' => $file->getClientOriginalName(),
                    'url' => \Illuminate\Support\Facades\Storage::url($path),
                    'path' => $path
                ];
            }
        }

        // Preparar dados para update
        $data = $validated;
        unset($data['password']); // Remover da array principal para não quebrar se estiver vazio
        unset($data['attachments']);

        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $data['documents'] = !empty($documents) ? $documents : null;

        $student->update($data);

        return redirect()->route('students.index')->with('success', 'Cadastro do aluno atualizado!');
    }
}