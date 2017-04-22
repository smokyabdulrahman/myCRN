<?php

namespace App\Http\Controllers;

use App\Course;
use App\Request;
use App\RegistrarParser;
use Illuminate\Support\Facades\Input;

class registrarController extends Controller
{
    public function update(){

        $registrar = new RegistrarParser();
        return $registrar->getAllHtmlPagesAndUpdate();
    }

    public function notify(){
        Request::notifyOpenCourses();
    }

    public function build(){
        $registrar = new RegistrarParser();
        return $registrar->getAllHtmlPagesAndBuild();
    }

    public function create(){
        $input = Input::all();
        $request = new Request();

        $course = Course::where('crn', '=', (integer)$input['crn'])->first();

        //check if course is open, then do not create the request
        if($course->status == 1){
            return view('open');
        }

        //check if there is a duplicate record
        $duplicate = Request::where('course_id', (integer)$input['crn'])
                            ->where('email', $input['email'])
                            ->first();
        if(!empty($duplicate))
            return view('duplicate');

        $request->email = $input['email'];
        $request->phone = $input['phone'];
        $request->course_id = (integer)$input['crn'];
        $request->save();

        echo "done";

        return view('welcome');
    }
}
