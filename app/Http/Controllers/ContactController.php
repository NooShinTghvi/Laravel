<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    private $imagePath = 'contact/image/';
    private $permittedChars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-";

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:25',
            'last_name' => 'required|max:45',
            'phone' => 'required|digits:11',
            'image' => 'nullable|image|max:1000',
        ]);
        if ($validator->fails()) {
            return $this->mrResponse('error', $validator->errors()->all());
        }
        $contact = Contact::create([
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'phone' => $request->get('phone'),
            'user_id' => Auth::id()
        ]);
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $this->saveImageInStorage($request, $contact);
        }

        return $this->mrResponse('success', ['done']);
    }

    public function getLegalCases()
    {
        return Contact::where('user_id', Auth::id())->get();
    }

    public function get()
    {
        return Contact::all();
    }

    public function findContactsFromOwnerOfGroup($group)
    {
        $groupController = new GroupController();
        $user = $groupController->getOwnerOfUser($group);
        return $user->contacts()->get();
    }

    public function isAllowAccess($contactId)
    {
        $contact = Contact::where('id', $contactId)->where('user_id', Auth::id())->first();
        if (is_null($contact))
            return [
                'status' => false,
            ];
        else
            return [
                'status' => true,
                'contact' => $contact
            ];
    }

    public function edit($contactId)
    {
        $statusAccess = $this->isAllowAccess($contactId);
        if (!$statusAccess['status'])
            return abort(404);
        $contact = $statusAccess['contact'];

        $groupController = new GroupController();
        // find groups that contact is part of that
        $contactInGroups = $groupController->findGroupsForSpecificContact($contact);
        $groups = $groupController->get();

        return view('livewire.edit-contact', [
            'contact' => $contact,
            'contactInGroups' => $contactInGroups,
            'groups' => $groups
        ]);
    }

    private function saveImageInStorage(Request $request, $contact)
    {
        $imageExtension = '.' . $request->image->extension();
        $imageName = substr(str_shuffle($this->permittedChars), 0, 15) . $imageExtension;
        while (Contact::where('image_path', $this->imagePath . $imageName)->exists())
            $imageName = substr(str_shuffle($this->permittedChars), 0, 15) . $imageExtension;
        Storage::putFileAs($this->imagePath, $request->image, $imageName);
        $contact->image_path = $this->imagePath . $imageName;
        $contact->save();
    }

    public function saveChanges(Request $request, $contactId)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:25',
            'last_name' => 'required|max:45',
            'phone' => 'required|digits:11',
            //'phone' => 'required|digits:11,13|max:10000000000000|min:9999999999',
        ]);
        if ($validator->fails()) {
            return $this->mrResponse('error', $validator->errors()->all());
        }

        $statusAccess = $this->isAllowAccess($contactId);
        if (!$statusAccess['status'])
            return $this->mrResponse('error', ['contact not found.']);
        $contact = $statusAccess['contact'];

        $contact->first_name = $request->input('first_name');
        $contact->last_name = $request->input('last_name');
        $contact->phone = $request->input('phone');
        $contact->save();

        return $this->mrResponse('success', ['done']);
    }

    public function changeImage(Request $request, $contactId)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|max:1000|image|mimes:jpeg,png,jpg,svg',
        ]);
        if ($validator->fails()) {
            return $this->mrResponse('error', $validator->errors()->all());
        }

        $statusAccess = $this->isAllowAccess($contactId);
        if (!$statusAccess['status'])
            return $this->mrResponse('error', ['selected contact is not yours.']);
        $contact = $statusAccess['contact'];

        if (!is_null($contact->image_path)) {
            if (Storage::exists($this->findImagePath($contact->image_path)))
                Storage::delete($this->findImagePath($contact->image_path));
            else return $this->mrResponse('error', ['internal error']);
        }

        $this->saveImageInStorage($request, $contact);
        return $this->mrResponse('success', [$contact->image_path]);
    }

    private function findImagePath($path)
    {
        $a = explode('/', $path);
        return $this->imagePath . '/' . end($a);
    }

    public function getContactsOfGroup($group)
    {
        return $group->contacts()->get();
    }

    public function delete($contactId)
    {
        if (!Contact::where('id', $contactId)->where('user_id', Auth::id())->exists())
            return 'not possible';
        Contact::where('id', $contactId)->delete();
        ContactGroup::where('contact_id', $contactId)->delete();
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
