<?php

namespace App\Http\Controllers;

use App\Models\ContactGroup;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:30|min:3',
        ]);
        if ($validator->fails()) {
            return $this->mrResponse('error', $validator->errors()->all());
        }
        $name = $request->get('name'); //name for new group
        $result = Group::where('user_id', Auth::id())->where('name', $name)->first();
        if (!is_null($result))
            return $this->mrResponse('error', ['The selected name is duplicate.']);

        Group::create([
            'name' => $name,
            'user_id' => Auth::id(),
        ]);
        return $this->mrResponse('success', ['done']);
    }

    public function get()
    {
        return Group::where('user_id', Auth::id())->get();
    }

    public function getAll()
    {
        return Group::all();
    }

    public function getOwnerOfUser($group)
    {
        return $group->user;
    }

    public function findGroupsForSpecificContact($contact)
    {
        return $contact->groups()->get();
    }

    public function edit($groupId)
    {
        $group = Group::where('id', $groupId)->where('user_id', Auth::id())->first();
        if (is_null($group))
            return abort(404);

        $contactController = new ContactController();
        return view('livewire.edit-group', [
            'contacts' => $contactController->getContactsOfGroup($group),
            'group' => $group
        ]);
    }

    public function saveChanges(Request $request, $groupId)
    {
        $group = Group::where('id', $groupId)->where('user_id', Auth::id())->first();
        if (is_null($group))
            return $this->mrResponse('error', ['selected group is not yours.']);

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:30|min:3',
        ]);
        if ($validator->fails()) {
            return $this->mrResponse('error', $validator->errors()->all());
        }

        $groupName = $request->input('name');
        if (Group::where('user_id', Auth::id())->where('name', $groupName)->exists())
            return $this->mrResponse('error', ['chosen name is repeated.']);

        $group->name = $request->input('name');
        $group->save();

        return $this->mrResponse('success', ['done']);

    }

    public function addTo(Request $request, $contactId)
    {
        $contactController = new ContactController();
        $statusAccess = $contactController->isAllowAccess($contactId);
        if (!$statusAccess['status'])
            return $this->mrResponse('error', ['selected contact is not yours.']);

        if (!$request->filled('group') | $request->input('group') == 0)
            return $this->mrResponse('error', ['Item not selected.']);

        $group = Group::where('user_id', Auth::id())->where('id', $request->input('group'))->first();
        if (is_null($group))
            return $this->mrResponse('error', ['chosen group is not yours.']);

        if (ContactGroup::where('group_id', $group->id)->where('contact_id', $contactId)->exists())
            return $this->mrResponse('error', ['chosen group is repeated.']);

        ContactGroup::create([
            'group_id' => $group->id,
            'contact_id' => $contactId
        ]);

        return $this->mrResponse('success', [$group->name]);
    }

    public function deleteFrom(Request $request, $groupId)
    {
        $group = Group::where('id', $groupId)->where('user_id', Auth::id())->first();
        if (is_null($group))
            return $this->mrResponse('error', ['selected group is not yours.']);

        if (!$request->filled('contact') | $request->input('contact') == 0)
            return $this->mrResponse('error', ['Item not selected.']);

        $contactId = $request->input('contact');
        if (!ContactGroup::where('group_id', $groupId)->where('contact_id', $contactId)->exists())
            return $this->mrResponse('error', ['sth is wrong, try again.']);

        ContactGroup::where('group_id', $groupId)->where('contact_id', $contactId)->delete();
        return $this->mrResponse('success', [$contactId]);
    }

    public function delete($groupId)
    {
        if (!Group::where('id', $groupId)->where('user_id', Auth::id())->exists())
            return 'not possible';
        Group::find($groupId)->delete();
        ContactGroup::where('group_id', $groupId)->delete();
        return redirect('dashboard');
    }

    private function mrResponse($status, $msg)
    {
        return [
            'status' => $status,
            'data' => $msg,
        ];
    }
}
