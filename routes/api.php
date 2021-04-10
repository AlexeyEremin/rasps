<?php

use \App\Http\Controllers\UserController;
use \App\Http\Controllers\TeacherController;
use \App\Http\Controllers\SubjectController;
use \App\Http\Controllers\GroupController;
use \App\Http\Controllers\TimetableController;
use \App\Http\Controllers\RelateditemController;

Route::post('login', [UserController::class, 'login']);


Route::middleware(['userAuth'])->group(function() {
    Route::get('teachers', [TeacherController::class, 'show']);
    Route::post('teachers', [TeacherController::class, 'add']);
    Route::post('teachers/subject', [TeacherController::class, 'subject']);
    Route::post('teachers/subject/delete', [TeacherController::class, 'subjectDelete']);
    Route::get('teachers/timetable/{date}', [TeacherController::class, 'timetableTeacher']);
    Route::get('teachers/{teacher}', [TeacherController::class, 'firstTeacher']);
    Route::post('teacher/{teacher}', [TeacherController::class, 'editTeacher']);

    Route::get('subjects', [SubjectController::class, 'show']);
    Route::post('subjects', [SubjectController::class, 'add']);
    Route::post('subject/{subject}', [SubjectController::class, 'edit']);

    Route::get('groups', [GroupController::class, 'show']);
    Route::post('groups', [GroupController::class, 'add']);
    Route::post('group/{group}', [GroupController::class, 'edit']);
    Route::post('group/{group}/position/{position}', [GroupController::class, 'editPosition']);
    Route::get('groups/related/{group}', [RelateditemController::class, 'show']);
    Route::post('groups/related', [RelateditemController::class, 'add']);
    Route::delete('groups/related/{group}/{relatedItem}', [RelateditemController::class, 'destroy']);

    Route::get('timetable', [TimetableController::class, 'show']);
    Route::post('timetable/save', [TimetableController::class, 'saveTGTs']);
    Route::post('timetable/first/save', [TimetableController::class, 'saveOneTGTs']);
    Route::get('timetable/{date}', [TimetableController::class, 'open']);
    Route::post('timetable', [TimetableController::class, 'add']);
});
