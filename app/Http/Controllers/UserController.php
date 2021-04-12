<?php /** @noinspection ALL */

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\County;
use App\Models\EducationBase;
use App\Models\Field;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function updateInformation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'melli_code' => 'required|size:10|unique:users',
            'education_base_select' => 'required|exists:education_bases,id',
            'field_select' => 'required|exists:fields,id',
            'city_select' => 'required|exists:city,id',
            'image' => 'max:2500',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $user = $this->getUser();
        $user->melli_code = $request->input('melli_code');
        $user->education_base_id = $request->input('education_base_select');
        $user->field_id = $request->input('field_select');
        $user->city_id = $request->input('city_select');

        if ($request->hasFile('image')) {
            if ($user->melli_image_path != '') {
                Storage::delete($user->melli_image_path);
            }
            $newName = $this->getName(15) . '.' . $request->file('image')->extension();
            while (Storage::exists($newName)) {
                $newName = $this->getName(15) . '.' . $request->file('image')->extension();
            }
            Storage::putFileAs('/', $request->file('image'), $newName);
            $user->melli_image_path = 'storage/' . $newName;
        } else if ($user->melli_image_path == '') {
            return response(['image' => 'upload Melli Code Image.'], 422);
        }
        $user->save();
        return response(null, 204);
    }

    public function getName($n)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }

    public function information()
    {
        $user = $this->getUser();
        $provinces = Province::all();
        $education_bases = EducationBase::all();
        $fields = Field::all();
        return response([
            'user' => $user,
            'education_bases' => $education_bases,
            'fields' => $fields,
            'provinces' => $provinces,
        ]);
    }

    public function getCountiesByProvince($provinceId)
    {
        return response(['counties' => County::where('province_id', $provinceId)->get()]);
    }

    public function getCitiesByProvinceANDCounty($provinceId, $countyId)
    {
        return response(['cities' => City::where('province_id', $provinceId)->where('county_id', $countyId)->get()]);
    }
}
