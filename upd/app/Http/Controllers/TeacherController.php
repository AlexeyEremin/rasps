<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Teacher;
use App\Models\TeacherSubject;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    /**
     * Show all Teacher
     * @return Teacher[]|\Illuminate\Database\Eloquent\Collection
     */
    public function show() {
        return Teacher::all();
    }

    /**
     * Add Teacher
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function add(Request $request) {
        $valid = Validator::make($request->all(), [
            'fullname' => 'required',
            'color' => 'required',
        ]);

        if($valid->fails())
            return response($valid->errors(), 400);

        $data = [
            'fullname' => $request->fullname,
            'color' => $request->color,
            'user_id' => Token::securityToken($request)->user_id
        ];

        Teacher::create($data);
        return response([], 201);
    }

    /**
     * Add Subject to Teacher
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function subject(Request $request) {
        $valid = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        if($valid->fails())
            return response($valid->errors(), 400);

        $data = [
            'teacher_id' => $request->teacher_id,
            'subject_id' => $request->subject_id,
            'user_id' => Token::securityToken($request)->user_id
        ];

        TeacherSubject::create($data);
        return response([], 201);
    }

    public function subjectDelete(Request $request) {
        $valid = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        if($valid->fails())
            return response($valid->errors(), 400);

        $delete = TeacherSubject::where([
            'teacher_id' => $request->teacher_id,
            'subject_id' => $request->subject_id
        ])->delete();
        if(!$delete)
            return response([], 400);

        return response([], 200);
    }

    public function firstTeacher(Request $request, Teacher $teacher) {
        return $teacher->subjects();
    }

    public function timetableTeacher(Request $request, $date) {
        // Задача
        // Получить всех педагогов
        // Получить предметы в которых они ведут
        // Получить группы по номеру пары

        $teachers = Teacher::all();
        $timetable = [];
        $timetable['timetable'] = $date;
        foreach($teachers as $teacher)
            $timetable['teachers'][] = $teacher->timetable($date);

        $timetable['groups'] = Group::select(['name', 'id'])->get();
        return $timetable;
    }

    public function editTeacher(Request $request, Teacher $teacher) {
        $teacher->color = $request->color;
        $teacher->fullname = $request->fullname;
        $teacher->save();
        return response([], 201);
    }
}
