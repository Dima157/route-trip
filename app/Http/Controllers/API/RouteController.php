<?php

namespace App\Http\Controllers\API;

use App\Services\RouteService;
use App\Services\XMLConverter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RouteController extends Controller
{
    public function details(Request $request)
    {
        try {
            if(!$request->hasFile('file')) {
                return response()->json(['success' => false, 'error' => 'File is not upload'], 401);
            }
            $file = $request->file('file');
            $file->move(public_path(),substr(md5(microtime()),rand(0,26),5) .".xml");
            $route = (new XMLConverter(realpath(public_path($file))))->xmlToArray();
            list($trip, $breakPoints, $time) = (new RouteService())->getTripInfo($route);
            return response()->json(['success' => true, 'route' => $trip, 'breakPoints' => $breakPoints, 'spendTime' => $time], 200);
        } catch (\Throwable $ex) {
            return response()->json(['success' => false, 'error' => $ex->getMessage()], 401);
        }
    }
}
