<?php

namespace App\Http\Controllers;

use App\RegistrarParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class registrarController extends Controller
{
    public function update(){

        $registrar = new RegistrarParser();
        return $registrar->getAllHtmlPagesAndUpdate();
    }

    public function notify(){
        \App\Request::notifyOpenCourses();
    }

    public function build(){
        $registrar = new RegistrarParser();
        return $registrar->getAllHtmlPagesAndBuild();
    }

    public function create(){
        $input = Input::all();
        $request = new \App\Request();

        $request->email = $input['email'];
        $request->phone = $input['phone'];
        $request->course_id = (integer)$input['crn'];
        $request->save();

        echo "done";

        return view('welcome');
    }
}
