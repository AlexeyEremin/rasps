<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Token;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function show() {
        return Group::orderBy('position', 'ASC')->get();
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

        Group::create($data);
        return response([], 201);
    }

    public function edit(Request $request, Group $group) {
        $group->name = $request->name;
        $group->save();
        return response([], 201);
    }

    public function editPosition(Request $request, Group $group, $position) {
        $group->position = $request->position;
        $group->save();
        return response([], 201);
    }
}
