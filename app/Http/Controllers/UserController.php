<?php

namespace App\Http\Controllers;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Kavenegar\Exceptions\ApiException;
use Kavenegar\Exceptions\HttpException;
use Kavenegar\KavenegarApi;

class UserController extends Controller
{
    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateInformation(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'lname' => 'required|max:255',
            'nationalCode' => ['required', 'size:10', Rule::unique('users', 'melli_code')->ignore(session()->get('userId'))],
            'phone' => ['required', 'size:11', Rule::unique('users', 'mobile')->ignore(session()->get('userId'))],
            'education-base-select' => 'required|exists:education_bases,id',
            'field-select' => 'required|exists:fields,id',
            'city-select' => 'required|exists:city,id',
            'image' => 'max:2500',
        ], [
            'name.required' => 'وارد کردن نام اجباری است',
            'name.max' => 'تعداد کاراکتر وارد شده برای نام بیش از حد مجاز است',
            'lname.required' => 'وارد کردن نام خانوادگی اجباری است',
            'lname.max' => 'تعداد کاراکتر وارد شده برای نام خانوادگی بیش از حد مجاز است',
            'nationalCode.required' => 'وارد کردن کد ملی اجباری است',
            'nationalCode.size' => 'تعداد ارقام وارد شده اشتباه است',
            'nationalCode.unique' => 'کد ملی وارد شده ثبت شده است',
            'phone.required' => 'وارد کردن شماره ی موبایل اجباری است',
            'phone.size' => 'تعداد ارقام وارد شده موبایل اشتباه است',
            'phone.unique' => 'شماره ی موبایل ثبت شده است',
            'education-base-select.required' => 'انتخاب پایه تحصیلی ضروری است',
            'education-base-select.exists' => 'ارسال اطلاعات ناموفق بوده است',
            'field-select.required' => 'انتخاب رشته ی تحصیلی ضروری است',
            'field-select.exists' => 'ارسال اطلاعات ناموفق بوده است',
            'city-select.exists' => 'ارسال اطلاعات ناموفق بوده است',
            'city-select.required' => 'انتخاب شهر ضروری است',
            'image.required' => 'عکس کارت ملی خود را آپلود کنید',
            'image.max' => 'حجم عکس ارسالی زیاد است'
        ]);

        $user = User::find(session()->get('userId'));
        $user->first_name = $request->input('name');
        $user->last_name = $request->input('lname');
        $user->melli_code = $request->input('nationalCode');
        $user->mobile = $request->input('phone');
        $user->education_base_id = $request->input('education-base-select');
        $user->field_id = $request->input('field-select');
        $user->city_id = $request->input('city-select');

        if ($request->hasFile('image')) {
            if ($user->melli_image_path != '') {
                Storage::delete($user->melli_image_path);
            }
            $newName = $this->getName(15) . '.' . $request->file('image')->extension();
            while (Storage::exists($newName)) {
                $newName = $this->getName(15) . '.' . $request->file('image')->extension();
            }
            Storage::putFileAs('/', $request->file('image'), $newName);
            $user->melli_image_path = 'storage/'.$newName;
        }else if($user->melli_image_path == ''){
            return redirect()->back()->withErrors([
                'image' => 'عکس کارت ملی خود را آپلود کنید'
            ]);
        }
        $user->save();
        return redirect()->intended();
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
        $user = \auth('user')->user();
        $provinces = Province::all();
        $education_bases = EducationBase::get(['id', 'name']);
        return view('user::userprofile.updateInformation')
            ->with(compact('user'))
            ->with(compact('provinces'))
            ->with(compact('education_bases'));
    }

    public function getCountyByProvince(Request $request)
    {
        $province_id = $request->province_id;
        $counties = County::where('province_id', $province_id)->get();
        $res = "<option value=''>شهرستان</option>";
        foreach ($counties as $county) {
            $res .= '<option value="' . $county->id . '"">' . $county->name . '</option>';
        }
        return $res;
    }

    public function getCityByCounty(Request $request)
    {
        $province_id = $request->province_id;
        $county_id = $request->county_id;
        $cities = City::where('province_id', $province_id)->where('county_id', $county_id)->get();
        $res = "<option value=''>شهر</option>";
        foreach ($cities as $city) {
            $res .= '<option value="' . $city->id . '"">' . $city->name . '</option>';
        }
        return $res;
    }

    public function getFieldByEducationbase(Request $request)
    {
        $province_id = $request->education_base_id;
        if ($province_id > 9) {
            $fields = Field::all();
            $res = "<option value=''>رشته تحصیلی</option>";
            foreach ($fields as $field) {
                $res .= '<option value="' . $field->id . '"">' . $field->name . '</option>';
            }
            return json_encode(["isOK" => true, "html" => $res]);
        }
        return json_encode(["isOK" => false]);
    }


    public function otpSend(Request $request)
    {

        $this->validate($request, [
            'phone' => 'bail|required|numeric|digits:11|unique:users,mobile'
        ], [
            'phone.required' => 'وارد کردن شماره تماس اجباری است',
            'phone.numeric' => 'موبایل شامل عدد می باشد',
            'phone.digits' => 'تعداد ارقام وارد شده صحیح نمی باشد',
            'phone.unique' => 'این شماره موبایل قبلا ثبت شده است',
        ]);
        try {
            $api_key = env('KAVEHNEGARKEY');
            $receptor = $request->phone;
            session()->put('mobile', $receptor);
            $token = random_int(11111, 99999);
            $template = "roodiAzmoon";
            $api = new KavenegarApi($api_key);
            $api->VerifyLookup($receptor, $token, '11235', '22', $template);
            session(['otp' => $token]);
            $status = 200;
        } catch (ApiException $e) {
            $status = 201;
        } catch (HttpException $e) {
            $status = 202;
        }
        return json_encode(['status' => $status, 'mobile' => $receptor]);
    }

    public function otpValidate(Request $request)
    {
        $otp = \request()->session()->get('otp');
        $this->validate($request, [
            'code' => 'required|digits:5'
        ], [
            'code.required' => 'وارد کردن کد تایید اجباری است',
            'code.digits' => 'کد تایید باید ۵ رقمی باشد',
        ]);

        if ($request->code == $otp) {
            $mobile = \request()->session()->get('mobile');
            \request()->session()->remove('otp');
            \request()->session()->remove('mobile');
            return json_encode(['status' => 200, 'mobile' => $mobile]);
        }
        return json_encode(['status' => 404]);
    }
}
