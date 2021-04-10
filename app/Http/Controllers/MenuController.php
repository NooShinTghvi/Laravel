<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Modules\UserProfile\Transformers\MenuTransformer;
use Nwidart\Modules\Facades\Module;
use Illuminate\Support\Facades\Log;
use Spatie\Fractal\Fractal;
use function Couchbase\defaultDecoder;


class MenuController extends Controller
{
    private $allConfigs;
    private $myLogoPath = '';
    private $ModuleName = 'User';

    public function __construct(){
        $this->allConfigs =  $this->areModulesEnable();
    }

    public function areModulesEnable(){
        $allConfigs = collect();
        $availableModules = Config::get('user');
        for ($i=0 ; $i < sizeof($availableModules['features']) ; $i++) {
            if (Module::find($availableModules['features'][$i])) {
                $allConfigs->push(Config::get (strtolower($availableModules['features'][$i]) ));
            }
        }
        return $allConfigs;
    }

    public function wrapperContent($mainContent = '', $guard = ''){
        try {
            $myContent = [
                'allConfigs'    => $this->allConfigs,
                'mainContent'   => $mainContent,
                'logo'          =>
                    [
                        'isExists'  => Storage::disk()->exists($this->myLogoPath),
                        'url'       => Storage::url($this->myLogoPath),
                    ],
                'guard'         => $guard,
            ];
            return $myContent;
        }
        catch (Exception $e) {
            return view('user::userprofile.errorPage');
        }
    }

    public function profile(){
        try {
            return view('user::userprofile.profile')->with('myContent', $this->wrapperContent());
        }
        catch (Exception $e) {
            return view('user::userprofile.errorPage');
        }
    }

    public function bringContentOfAnotherPage(Request $request, $moduleName, $functionName, $id){
        try {
            $index_nameModule = 0;
            $index_item = 0;

            if ($moduleName == 'u'){ $moduleName = 'user';}

            for ($i = 0 ; $i < sizeof($this->allConfigs) ; $i++) {
                if (strtolower($this->allConfigs[$i]['name']) == $moduleName){
                    $index_nameModule = $i;
                    for ($j = 0; $j < sizeof($this->allConfigs[$i]['items'][$this->ModuleName]); $j++) {
                        if ($this->allConfigs[$i]['items'][$this->ModuleName][$j]['function'] == $functionName) {
                            $index_item = $j;
                        }
                    }
                }
            }

            $myObject = $this->allConfigs[$index_nameModule]['items'][$this->ModuleName][$index_item]['controller'];
            $myFunction = $this->allConfigs[$index_nameModule]['items'][$this->ModuleName][$index_item]['function'];
            $guard = $this->allConfigs[$index_nameModule]['items'][$this->ModuleName][$index_item]['guard'];
            Log::debug($myObject.' - '.$myFunction.' - '.$guard);
            if ($id == null)
                $mainContent = app($myObject)->{$myFunction}($request, $guard);
            else
                $mainContent = app($myObject)->{$myFunction}($request, $id, $guard);

            return [
                'index_nameModule'  => $index_nameModule,
                'index_item'        => $index_item,
                'mainContent'       => $mainContent,
                'guard'             => $guard,
            ];
        }
        catch (Exception $e) {
            return view('user::userprofile.errorPage');
        }
    }

    public function callRealFunction(Request $request, $moduleName, $functionName, $id = null) {
        try {
            $tmp = $this->bringContentOfAnotherPage($request, $moduleName, $functionName, $id);
            Log::critical($tmp);
            $index_nameModule   = $tmp['index_nameModule'];
            $index_item         = $tmp['index_item'];
            $mainContent        = $tmp['mainContent'];
            $guard              = $tmp['guard'];

            if ($this->allConfigs[$index_nameModule]['items'][$this->ModuleName][$index_item]['type'] == 'get'){
                if ($mainContent->getData()->status == 'OK') {
                    $mainContent = $mainContent->getData()->data->content;
                }
                else {
                    if ($mainContent->getData()->data->presentable){
                        $mainContent = $mainContent->getData()->data->errors;
                    }
                    else{
                        $mainContent = view('user::userprofile.errorPage')->render();
                    }
                }
                return view('user::userprofile.profile')->with('myContent', $this->wrapperContent($mainContent,$guard));
            }
            if ($this->allConfigs[$index_nameModule]['items'][$this->ModuleName][$index_item]['type'] == 'ajax'){

                if ($mainContent->getData()->status == 'OK'){
                    return $mainContent;

                }
                else {
                    if ($mainContent->getData()->data->presentable){
                        return $mainContent;

                    }
                    else{
                        $mainContent = view('user::userprofile.errorPage')->render();
                        return view('user::userprofile.profile')->with('myContent', $this->wrapperContent($mainContent));
                    }
                }
            }
            /*if ($this->allConfigs[$index_nameModule]['items'][$this->ModuleName][$index_item]['type'] == 'post'){
                if ($mainContent->getData()->status == 'OK'){
                    return redirect()->back()->with('success', $this->wrapperContent($mainContent)->getData()->massage);
                }
                else {
                    if ($mainContent->getData()->data->presentable) {
                        return redirect()->back()->withInput()->withErrors($this->wrapperContent($mainContent)->getData()->data->errors);
                    }
                    else {
                        $mainContent = view('user::userprofile.errorPage')->render();
                        return view('user::userprofile.profile')->with('myContent', $this->wrapperContent($mainContent));
                    }
                }
            }*/
        }
        catch (Exception $e){
            log::debug($e);
            return view('user::userprofile.errorPage');
        }
    }


}
