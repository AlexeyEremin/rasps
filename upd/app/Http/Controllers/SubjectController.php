<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    public function show() {
        return Subject::orderBy('name', 'ASC')->get();
    }

    public function add(Request $request) {
        $valid = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if($valid->fails())
            return response($valid->errors(), 400);

        $data = [
            'name' => $request->name,
            'user_id' => Token::securityToken($request)->user_id
        ];

        Subject::create($data);
        return response([], 201);
    }

    public function edit(Request $request, Subject $subject) {
        $subject->name = $request->name;
        $subject->save();
        return response([], 201);
    }
}
