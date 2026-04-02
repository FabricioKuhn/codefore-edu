<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;

class StudentClassroomController extends Controller
{
    public function show(Classroom $classroom)
    {
        abort_if(!$classroom->students()->where('users.id', request()->user()->id)->exists(), 403);

        $classroom->load('activities');

        return view('student.classrooms.show', compact('classroom'));
    }
}
