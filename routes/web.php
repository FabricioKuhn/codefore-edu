<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\PlanController;
use App\Http\Controllers\SuperAdmin\InstitutionController;
use App\Http\Middleware\IsSuperAdmin;
use App\Http\Controllers\ActivityController; // Para o Professor
use App\Http\Controllers\Student\ActivityController as StudentActivity; // Para o Aluno
use App\Http\Controllers\Teacher\DashboardController;


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
        
        Route::get('/dashboard', [DashboardController::class, 'adminIndex'])->name('dashboard');

        Route::resource('classrooms', \App\Http\Controllers\ClassroomController::class);
        Route::post('classrooms/{classroom}/students', [\App\Http\Controllers\ClassroomStudentController::class, 'store'])->name('classrooms.students.store');
        Route::resource('teachers', \App\Http\Controllers\TeacherController::class);
        Route::patch('teachers/{teacher}/toggle-status', [\App\Http\Controllers\TeacherController::class, 'toggleStatus'])->name('teachers.toggle-status');
        Route::resource('students', \App\Http\Controllers\StudentController::class)->except(['destroy']);
        Route::post('students/{student}/enroll', [\App\Http\Controllers\StudentController::class, 'enroll'])->name('students.enroll');
        Route::patch('students/{student}/toggle-status', [\App\Http\Controllers\StudentController::class, 'toggleStatus'])->name('students.toggle-status');
        Route::patch('activities/{activity}/students/{student}/deadline', [\App\Http\Controllers\ActivityController::class, 'updateStudentDeadline'])->name('activities.students.deadline');

        // ---> ADICIONE ESTAS LINHAS AQUI (Copiadas do professor) <---
        Route::resource('activities', \App\Http\Controllers\ActivityController::class);
        Route::resource('questions', \App\Http\Controllers\QuestionController::class);
        Route::post('lessons/{lesson}/attendance', [\App\Http\Controllers\LessonController::class, 'storeAttendance'])->name('lessons.attendance.store');
        Route::post('lessons/{lesson}/cancel', [\App\Http\Controllers\LessonController::class, 'cancel'])->name('lessons.cancel');
        Route::post('lessons/{lesson}/register', [\App\Http\Controllers\LessonController::class, 'register'])->name('lessons.register');
        Route::put('questions/{question}/status', [\App\Http\Controllers\QuestionController::class, 'updateStatus'])->name('questions.update_status');
        Route::post('activities/{activity}/students/{student}/toggle', [\App\Http\Controllers\ActivityController::class, 'toggleStudent'])->name('activities.students.toggle');
        // Novas rotas para o motor da V2 (Gestão de Provas e Tarefas)
        Route::post('activities/{activity}/questions/attach', [\App\Http\Controllers\ActivityController::class, 'attachQuestions'])->name('activities.questions.attach');
        Route::delete('activities/{activity}/questions/{question}/detach', [\App\Http\Controllers\ActivityController::class, 'detachQuestion'])->name('activities.questions.detach');
        Route::patch('activities/{activity}/questions/{question}/weight', [\App\Http\Controllers\ActivityController::class, 'updateQuestionWeight'])->name('activities.questions.update_weight');
        Route::patch('activities/{activity}/students/{student}/toggle', [\App\Http\Controllers\ActivityController::class, 'toggleStudent'])->name('activities.students.toggle');
});


// =======================================================================
// ÁREA DO PROFESSOR
// =======================================================================
Route::middleware(['auth', 'verified', \App\Http\Middleware\CheckRole::class.':teacher'])
    ->prefix('professor') // URL fica: escola.local/professor/...
    ->name('teacher.') 
    ->group(function () {
        
        Route::get('/dashboard', [DashboardController::class, 'teacherIndex'])->name('dashboard');

        // Rotas do dia a dia do professor (Aulas, Atividades, Chamada)
        Route::resource('activities', \App\Http\Controllers\ActivityController::class);
       Route::resource('questions', \App\Http\Controllers\QuestionController::class);
        Route::post('lessons/{lesson}/attendance', [\App\Http\Controllers\LessonController::class, 'storeAttendance'])->name('lessons.attendance.store');
        Route::post('lessons/{lesson}/cancel', [\App\Http\Controllers\LessonController::class, 'cancel'])->name('lessons.cancel');
        Route::post('lessons/{lesson}/register', [\App\Http\Controllers\LessonController::class, 'register'])->name('lessons.register');
        Route::put('questions/{question}/status', [\App\Http\Controllers\QuestionController::class, 'updateStatus'])->name('questions.update_status');
        Route::post('activities/{activity}/students/{student}/toggle', [\App\Http\Controllers\ActivityController::class, 'toggleStudent'])->name('activities.students.toggle');
        Route::patch('activities/{activity}/students/{student}/deadline', [\App\Http\Controllers\ActivityController::class, 'updateStudentDeadline'])->name('activities.students.deadline');
        // Espaços reservados para o menu do Professor
        // Rotas de Turmas do Professor (Sem permissão para criar ou deletar a turma em si)
        Route::resource('classrooms', \App\Http\Controllers\ClassroomController::class)->except(['create', 'store', 'destroy']);
        Route::get('/alunos', function() { return "Tela de Alunos do Professor em construção"; })->name('students.index');
        Route::post('classrooms/{classroom}/students', [\App\Http\Controllers\ClassroomStudentController::class, 'store'])->name('classrooms.students.store');
        Route::delete('classrooms/{classroom}/students/{student}', [\App\Http\Controllers\ClassroomStudentController::class, 'destroy'])->name('classrooms.students.destroy');
        // Novas rotas para o motor da V2 (Gestão de Provas e Tarefas)
        Route::post('activities/{activity}/questions/attach', [\App\Http\Controllers\ActivityController::class, 'attachQuestions'])->name('activities.questions.attach');
        Route::delete('activities/{activity}/questions/{question}/detach', [\App\Http\Controllers\ActivityController::class, 'detachQuestion'])->name('activities.questions.detach');
        Route::patch('activities/{activity}/questions/{question}/weight', [\App\Http\Controllers\ActivityController::class, 'updateQuestionWeight'])->name('activities.questions.update_weight');
        Route::patch('activities/{activity}/students/{student}/toggle', [\App\Http\Controllers\ActivityController::class, 'toggleStudent'])->name('activities.students.toggle');
       

        // Rotas de Correção (Painel do Professor)
    Route::prefix('activities/{activity}/submissions')->name('submissions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Teacher\SubmissionController::class, 'index'])->name('index');
        Route::get('/{submission}', [\App\Http\Controllers\Teacher\SubmissionController::class, 'show'])->name('show');
        Route::post('/{submission}/evaluate', [\App\Http\Controllers\Teacher\SubmissionController::class, 'evaluate'])->name('evaluate');
    });
});


// =======================================================================
// ÁREA DO ALUNO (Gamificação e Aulas)
// =======================================================================
Route::middleware(['auth', 'verified', \App\Http\Middleware\CheckRole::class.':student'])
    ->prefix('aluno')
    ->name('student.') 
    ->group(function () {
        
        Route::get('/dashboard', [DashboardController::class, 'studentIndex'])->name('dashboard');

        // Rotas do Aluno
        Route::get('classrooms/{classroom}', [\App\Http\Controllers\StudentClassroomController::class, 'show'])->name('classrooms.show');
        
        Route::get('/minha-turma', function() { return "Tela da Turma do Aluno em construção"; })->name('classrooms.index');
        Route::get('/feed', function() { return "Feed de gamificação em construção"; })->name('feed');
        Route::get('/student/classrooms/{classroom}/lessons/{lesson}', [App\Http\Controllers\StudentLessonController::class, 'show'])->name('student.lessons.show');
        Route::get('/classrooms/{classroom}/lessons/{lesson}', [\App\Http\Controllers\StudentLessonController::class, 'show'])->name('lessons.show');
    Route::post('/classrooms/{classroom}/lessons/{lesson}/complete', [\App\Http\Controllers\StudentLessonController::class, 'complete'])->name('lessons.complete');
        Route::post('/student/classrooms/{classroom}/lessons/{lesson}/complete', [App\Http\Controllers\StudentLessonController::class, 'complete'])->name('student.lessons.complete');

        Route::prefix('activities')->name('activities.')->group(function () {
    Route::get('/{activity}', [StudentActivity::class, 'show'])->name('show');
    Route::post('/{activity}/start', [StudentActivity::class, 'start'])->name('start');
    Route::get('/{activity}/play', [StudentActivity::class, 'play'])->name('play');
    Route::post('/{activity}/submit', [StudentActivity::class, 'submit'])->name('submit');
});
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
