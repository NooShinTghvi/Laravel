<?php

namespace App\Http\Controllers;

use App\Models\Contact;
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
            'phone' => 'required|max:13',
            'image' => 'required|max:1000|image|mimes:jpeg,png,jpg,svg',
        ]);
        if ($validator->fails()) {
            return [
                'status' => 'error',
                'data' => $validator->errors()->all(),
            ];
        }

        $contact = Contact::create([
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'phone' => $request->get('phone'),
            'user_id' => Auth::id()
        ]);

        if ($request->file('image')->isValid()) {
            $imageExtension = '.' . $request->image->extension();
            $imageName = substr(str_shuffle($this->permittedChars), 0, 15) . $imageExtension;
            while (Contact::where('image_path', $imageName)->exists())
                $imageName = substr(str_shuffle($this->permittedChars), 0, 15) . $imageExtension;
            Storage::putFileAs($this->imagePath, $request->image, $imageName);
            $contact->image_path = $imageName;
            $contact->save();
        }

        return [
            'status' => 'success',
            'data' => ['done',],
        ];
    }

}
