<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function convertKeys($array) {
        $array = $array->toArray();
        $result = [];

        foreach ($array as $key => $value) {
            $result[$key] = [];
            foreach ($value as $k => $v) {
                $uppercasedKey = ucfirst($k);
                $result[$key][$uppercasedKey] = $v;
            }
        }
        return $result;
    }
}
