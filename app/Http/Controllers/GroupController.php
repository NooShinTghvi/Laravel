<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:30',
        ]);
        if ($validator->fails()) {
            return [
                'status' => 'error',
                'data' => $validator->errors()->all(),
            ];
        }
        $name = $request->get('name'); //name for new group
        $result = Group::where('user_id', Auth::id())->where('name', $name)->first();
        if (!is_null($result))
            return [
                'status' => 'error',
                'data' => ['The selected name is duplicate.',],
            ];
        Group::create([
            'name' => $name,
            'user_id' => Auth::id(),
        ]);
        return [
            'status' => 'success',
            'data' => ['done',],
        ];
    }


}
