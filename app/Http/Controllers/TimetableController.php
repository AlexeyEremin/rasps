<?php

namespace App\Http\Controllers;

use App\Http\Resources\TimetableShow;
use App\Models\Group;
use App\Models\Timetable;
use App\Models\Token;
use App\Models\TGTs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PDF;

class TimetableController extends Controller
{
    public function show(Request $request) {
        return TimetableShow::collection(Timetable::where('user_id', Token::securityToken($request)->user_id)->orderBy('timetable', 'ASC')->get());
    }

    public function open(Request $request, $date) {
        return Timetable::where([
            ['timetable', '=', $date],
            ['user_id', '=', Token::securityToken($request)->user_id]
        ])->first()->firstOpen();
    }

    public function add(Request $request) {
        $valid = Validator::make($request->all(), ['timetable' => 'required']);

        if($valid->fails())
            return response($valid->errors(), 400);

        $data = [
            'timetable' => $request->timetable,
            'user_id' => Token::securityToken($request)->user_id
        ];

        $id = Timetable::create($data);
        foreach(Group::all() as $item)
            for($i = 1; $i < 9; $i++)
                for($b = 0; $b < 2; $b++)
                    TGTs::create([
                        'timetable_id' => $id->id,
                        'group_id' => $item->id,
                        'numeric' => $i,
                        'sub' => $b
                    ]);

        return response([], 201);
    }

    public function saveTGTs(Request $request) {
        foreach($request->all() as $tgtss)
            foreach($tgtss['tgts'] as $value) {
                $teacher_subject_id = ($value['teacher_subject_id'] == null || $value['teacher_subject_id'] == 'null' ? null : $value['teacher_subject_id']);
                $tgts = TGTs::find($value['id']);
                $tgts->teacher_subject_id = $teacher_subject_id;
                $tgts->lesson_type = $value['lesson_type'];
                $tgts->cabinet = $value['cabinet'];
                $tgts->save();
            }
        return response([], 201);
    }

    public function saveOneTGTs(Request $request) {
        $teacher_subject_id = ($request->teacher_subject_id == null || $request->teacher_subject_id == 'null' ? null : $request->teacher_subject_id);
        $tgts = TGTs::find($request->id);
        $tgts->teacher_subject_id = $teacher_subject_id;
        $tgts->lesson_type = $request->lesson_type;
        $tgts->cabinet = $request->cabinet;
        $tgts->save();

        return response([], 201);
    }


    public function copyTimetable(Request $request, Timetable $timetable) {
        
    }


    public function createPDFFiles(Request $request, $bearer, $date) {

        $request->headers->set('Authorization', 'Bearer ' . $bearer);

        $para = Timetable::where([
            ['timetable', '=', $date],
            ['user_id', '=', Token::securityToken($request)->user_id]
        ])->first()->firstOpen();

        $allPar = [];
        foreach($para['groups'] as $group)

        $pdf = PDF::loadView('pdf.template_student', ['para' => $para]);
        return $pdf->download('demo.pdf');
    }
}
