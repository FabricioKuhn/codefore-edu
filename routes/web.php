<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\PlanController;
use App\Http\Controllers\SuperAdmin\InstitutionController;
use App\Http\Middleware\IsSuperAdmin;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->role === 'student') {
        $user->load('classrooms');
    }
    return view('dashboard', compact('user'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('classrooms', \App\Http\Controllers\ClassroomController::class);
    Route::resource('classrooms.activities', \App\Http\Controllers\ActivityController::class)->shallow();
    Route::resource('activities.questions', \App\Http\Controllers\QuestionController::class)->shallow()->except(['create']);
    
    Route::post('classrooms/{classroom}/students', [\App\Http\Controllers\ClassroomStudentController::class, 'store'])->name('classrooms.students.store');
    Route::get('student/classrooms/{classroom}', [\App\Http\Controllers\StudentClassroomController::class, 'show'])->name('student.classrooms.show');

    Route::post('students/{student}/enroll', [\App\Http\Controllers\StudentController::class, 'enroll'])->name('students.enroll');
    Route::patch('students/{student}/toggle-status', [\App\Http\Controllers\StudentController::class, 'toggleStatus'])->name('students.toggle-status');
    Route::resource('students', \App\Http\Controllers\StudentController::class)->except(['destroy']);

    // Secretaria / Gestão de Alunos
    Route::post('students/{student}/enroll', [App\Http\Controllers\StudentController::class, 'enroll'])->name('students.enroll');
    Route::patch('students/{student}/toggle-status', [App\Http\Controllers\StudentController::class, 'toggleStatus'])->name('students.toggle-status');
    Route::resource('students', App\Http\Controllers\StudentController::class)->except(['destroy']);
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


