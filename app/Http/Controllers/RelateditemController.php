<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\RelatedItem;
use App\Models\Token;
use Illuminate\Http\Request;

class RelateditemController extends Controller
{
    public function add(Request $request) {
        $data = [
            'group_id' => $request->group_id,
            'ts_id' => $request->ts_id,
            'user_id' => Token::securityToken($request)->user_id
        ];
        RelatedItem::create($data);
        return response([], 201);
    }

    public function show(Request $request, Group $group) {
        return $group->getRelatedItem();

    }

    public function destroy($group, $ts_id) {
        RelatedItem::where([
            ['group_id', '=', $group],
            ['ts_id', '=', $ts_id],
        ])->first()->delete();
        return response(null, 201);
    }
}
