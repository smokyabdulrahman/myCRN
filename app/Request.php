<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use PHPMailer;

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

            //Create a new PHPMailer instance
            $mail = new PHPMailer;
            $mail->isSMTP();
// change this to 0 if the site is going live
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = 'html';
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 587;
            $mail->SMTPSecure = 'tls';

            //use SMTP authentication
            $mail->SMTPAuth = true;
//Username to use for SMTP authentication
            $mail->Username = "a.alrahama@gmail.com";
            $mail->Password = '$moky1417';
            $mail->addAddress($request->email, 'Somebody');
            $mail->Subject = 'Open!';
            // $message is gotten from the form
            $mail->msgHTML("<h1>$request</h1>
<h2>hurry go register the course!!</h2>");
            if (!$mail->send()) {
                echo "We are extremely sorry to inform you that your message
could not be delivered,please try again.";
            } else {
                echo "Your message was successfully delivered,you would be contacted shortly.";
            }

//            $to      = 'a.alrahama@gmail.com';
//            $subject = 'the subject';
//            $message = 'hello';
//            $headers = 'X-Mailer: PHP/' . phpversion();
//
//            mail($to, $subject, $message, $headers);

        }
    }
}
