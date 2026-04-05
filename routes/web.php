<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\PlanController;
use App\Http\Controllers\SuperAdmin\InstitutionController;
use App\Http\Middleware\IsSuperAdmin;


Route::get('/', function () {
    return view('welcome');
});

// -----------------------------------------------------------------------
// ENCRUZILHADA DO DASHBOARD ANTIGO (Redirecionamento de Segurança)
// -----------------------------------------------------------------------
Route::get('/dashboard', function () {
    $user = auth()->user();
    return match ($user->role) {
        'super_admin' => redirect()->route('superadmin.dashboard'),
        'admin' => redirect('/admin/dashboard'),
        'teacher' => redirect('/professor/dashboard'),
        'student' => redirect('/aluno/dashboard'),
        default => redirect('/'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');


// =======================================================================
// ÁREA DO ADMIN DA INSTITUIÇÃO (Tenant)
// =======================================================================
// =======================================================================
// ÁREA DO ADMIN DA INSTITUIÇÃO (Tenant)
// =======================================================================
Route::middleware(['auth', 'verified', \App\Http\Middleware\CheckRole::class.':admin'])
    ->prefix('admin') 
    ->name('admin.')  
    ->group(function () {
        
        Route::get('/dashboard', function () {
            $user = auth()->user();
            return view('dashboard', compact('user'));
        })->name('dashboard');

        Route::resource('classrooms', \App\Http\Controllers\ClassroomController::class);
        Route::post('classrooms/{classroom}/students', [\App\Http\Controllers\ClassroomStudentController::class, 'store'])->name('classrooms.students.store');
        Route::resource('teachers', \App\Http\Controllers\TeacherController::class);
        Route::patch('teachers/{teacher}/toggle-status', [\App\Http\Controllers\TeacherController::class, 'toggleStatus'])->name('teachers.toggle-status');
        Route::resource('students', \App\Http\Controllers\StudentController::class)->except(['destroy']);
        Route::post('students/{student}/enroll', [\App\Http\Controllers\StudentController::class, 'enroll'])->name('students.enroll');
        Route::patch('students/{student}/toggle-status', [\App\Http\Controllers\StudentController::class, 'toggleStatus'])->name('students.toggle-status');

        // ---> ADICIONE ESTAS LINHAS AQUI (Copiadas do professor) <---
        Route::resource('activities', \App\Http\Controllers\ActivityController::class);
        Route::resource('activities.questions', \App\Http\Controllers\QuestionController::class)->shallow()->except(['create']);
        Route::post('lessons/{lesson}/attendance', [\App\Http\Controllers\LessonController::class, 'storeAttendance'])->name('lessons.attendance.store');
        Route::post('lessons/{lesson}/cancel', [\App\Http\Controllers\LessonController::class, 'cancel'])->name('lessons.cancel');
        Route::post('lessons/{lesson}/register', [\App\Http\Controllers\LessonController::class, 'register'])->name('lessons.register');
        Route::put('questions/{question}/status', [\App\Http\Controllers\QuestionController::class, 'updateStatus'])->name('questions.update_status');
        Route::post('activities/{activity}/students/{student}/toggle', [\App\Http\Controllers\ActivityController::class, 'toggleStudent'])->name('activities.students.toggle');
});


// =======================================================================
// ÁREA DO PROFESSOR
// =======================================================================
Route::middleware(['auth', 'verified', \App\Http\Middleware\CheckRole::class.':teacher'])
    ->prefix('professor') // URL fica: escola.local/professor/...
    ->name('teacher.') 
    ->group(function () {
        
        Route::get('/dashboard', function () {
            return view('dashboard'); // Depois criaremos uma view só para o professor
        })->name('dashboard');

        // Rotas do dia a dia do professor (Aulas, Atividades, Chamada)
        Route::resource('activities', \App\Http\Controllers\ActivityController::class);
        Route::resource('activities.questions', \App\Http\Controllers\QuestionController::class)->shallow()->except(['create']);
        Route::post('lessons/{lesson}/attendance', [\App\Http\Controllers\LessonController::class, 'storeAttendance'])->name('lessons.attendance.store');
        Route::post('lessons/{lesson}/cancel', [\App\Http\Controllers\LessonController::class, 'cancel'])->name('lessons.cancel');
        Route::post('lessons/{lesson}/register', [\App\Http\Controllers\LessonController::class, 'register'])->name('lessons.register');
        Route::put('questions/{question}/status', [\App\Http\Controllers\QuestionController::class, 'updateStatus'])->name('questions.update_status');
        Route::post('activities/{activity}/students/{student}/toggle', [\App\Http\Controllers\ActivityController::class, 'toggleStudent'])->name('activities.students.toggle');
        // Espaços reservados para o menu do Professor
        Route::get('/turmas', function() { return "Tela de Turmas do Professor em construção"; })->name('classrooms.index');
        Route::get('/alunos', function() { return "Tela de Alunos do Professor em construção"; })->name('students.index');
});


// =======================================================================
// ÁREA DO ALUNO (Gamificação e Aulas)
// =======================================================================
Route::middleware(['auth', 'verified', \App\Http\Middleware\CheckRole::class.':student'])
    ->prefix('aluno') // URL fica: escola.local/aluno/...
    ->name('student.') 
    ->group(function () {
        
        Route::get('/dashboard', function () {
            $user = auth()->user();
            $user->load('classrooms');
            return view('dashboard', compact('user')); // Depois criaremos a view gamificada
        })->name('dashboard');

        // Rotas do Aluno
        Route::get('classrooms/{classroom}', [\App\Http\Controllers\StudentClassroomController::class, 'show'])->name('classrooms.show');
        // Espaços reservados para o menu do Aluno
        Route::get('/minha-turma', function() { return "Tela da Turma do Aluno em construção"; })->name('classrooms.index');
        Route::get('/feed', function() { return "Feed de gamificação em construção"; })->name('feed');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rotas do Super Admin (Agora protegidas pelo segurança IsSuperAdmin)
Route::middleware(['auth', IsSuperAdmin::class])->prefix('admin-codeforce')->name('superadmin.')->group(function () {
    
    // Dashboard do Super Admin
    Route::get('/dashboard', function () {
        return view('superadmin.dashboard');
    })->name('dashboard');

    // Gestão de Instituições / White Label
    Route::resource('institutions', \App\Http\Controllers\SuperAdmin\InstitutionController::class);

    // Rota específica para alternar o status da instituição
Route::patch('institutions/{institution}/toggle-status', [InstitutionController::class, 'toggleStatus'])->name('institutions.toggle-status');

Route::resource('institutions', InstitutionController::class);
    
});

Route::middleware(['auth', 'is_superadmin']) // Seus middlewares de segurança
    ->prefix('admin-codeforce')              // O prefixo da URL
    ->name('superadmin.')                    // O prefixo do NOME da rota (Importante!)
    ->group(function () {
        
        // Rota das Instituições que já funciona
        Route::resource('institutions', InstitutionController::class);

        // AQUI ESTÁ O QUE FALTA:
        Route::resource('plans', PlanController::class);
        
        // Rota para o botão de Ativar/Inativar
        Route::patch('plans/{plan}/toggle', [PlanController::class, 'toggleStatus'])->name('plans.toggle');
        
    });


require __DIR__.'/auth.php';
