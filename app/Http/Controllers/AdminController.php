<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function changeAccessingForContacts(Request $request)
    {
        if (!$request->filled('userAccess'))
            return $this->mrResponse('error', ['u dont choose any user.']);
        $userIds = $request->input('userAccess'); // user Ids for changing access
        for ($i = 0; $i < sizeof($userIds); $i++) {
            if (User::where('id', $userIds[$i])->exists()) {
                $user = User::find($userIds[$i]);
                $user->is_active = !$user->is_active;
                $user->save();
            } else
                return $this->mrResponse('error', ['chosen user not found.']);

        }
        return $this->mrResponse('success', $userIds);
    }

    private function mrResponse($status, $msg)
    {
        return [
            'status' => $status,
            'data' => $msg,
        ];
    }
}
