<?php

namespace App\Http\Controllers;

use App\RegistrarParser;
use Illuminate\Http\Request;

class registrarController extends Controller
{
    public function update(){

        $registrar = new RegistrarParser();
        return $registrar->getAllHtmlPagesAndUpdate();
    }
}
