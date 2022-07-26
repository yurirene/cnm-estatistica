<?php

namespace App\Http\Controllers;

use App\Services\EnviarMsgService;
use App\Services\IClaudiaService;
use Illuminate\Http\Request;

class IClaudiaController extends Controller
{

    public static function process($request)
    {

        if (!isset($request['message'])) {
            return;
        }

        IClaudiaService::processar($request);
    }

   
}
