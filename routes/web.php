<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Request;
use App\RegistrarParser;
Route::get('/', function () {

//    $course = new \App\Course();
//    $course->status = false;
//    $course->name = 'ICS202';
//    $course->time = '10:00am - 10:50am';
//    $course->days = 'UTR';
//    $course->crn = 35422;
//    $course->save();
//
//    $request = new Request();
//    $request->email = 'a@a.a';
//    $request->phone = '0556664545';
//    $request->course_id = 35421;
//    $request->save();

//    $request = new Request();
//    $request->email = 'b@b.b';
//    $request->phone = '055465';
//    $request->course_id = 35422;
//    $request->save();

//    $courses = \App\Course::where('status', '=', '1')->with('requests')->get();
//
//    foreach ($courses as $course){
//        echo $course->requests;
//        echo '<br>';
//    }
    $registrarParser = new RegistrarParser();
    $html = $registrarParser->getAllHtmlPages();
    foreach ($html as $dept)
        echo $dept;
    return view('welcome');
});
