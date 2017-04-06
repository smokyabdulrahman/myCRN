<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Request extends Model
{

    public function course(){
        return $this->belongsTo('App\Course');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'phone', 'course_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    public static function notifyOpenCourses(){
        $requests = Course::has('requests')->where('status', '=', '1')->get();
        $requests->load('requests');

        foreach ($requests as $request){
//            Mail::queue('emails.openCourse', ['request' => $request], function ($message)
//            {
//
//                $message->from('a.alrahama@gmail.com', 'Christian Nwamba');
//
//                $message->to('a.alrahama@gmail.com');
//
//            });

            $to      = 'a.alrahama@gmail.com';
            $subject = 'the subject';
            $message = 'hello';
            $headers = 'X-Mailer: PHP/' . phpversion();

            mail($to, $subject, $message, $headers);

        }
    }
}
