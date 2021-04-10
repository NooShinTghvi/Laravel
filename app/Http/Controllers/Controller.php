<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function callFunctionFromAnotherModule($configName, $moduleName, $name, $inputs)
    {
        $CoinConfig = Config::get($configName);
        for ($i = 0; $i < sizeof($CoinConfig['items'][$moduleName]); $i++) {
            if ($CoinConfig['items'][$moduleName][$i]['name'] == $name) {
                $myObject = app($CoinConfig['items'][$moduleName][$i]['controller']);
                $myFunction = $CoinConfig['items'][$moduleName][$i]['function'];
                $myObject->{$myFunction}($inputs);
            }
        }

    }

    public static function ReturnParentValue($class, $identifier, $variableName, &$data, $variableDesc): array
    {
        $Temp = [];
        foreach ($data as $key => $d) {
            if (empty($Temp[$d[$identifier]])) {
                $data[$key][$variableDesc] = $class::where('id', $d[$identifier])->value($variableName);
                $Temp[$d[$identifier]] = $data[$key][$variableDesc];
            } else
                $data[$key][$variableDesc] = $Temp[$d[$identifier]];
//            unset($data[$key][$identifier]);
        }
        return $data;
    }
}
