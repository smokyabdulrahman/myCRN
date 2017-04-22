<?php

namespace App\Console;

use App\Course;
use App\RegistrarParser;
use App\Request;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        //update courses
        $schedule->call(function(){
            $registrar = new RegistrarParser();
            $registrar->getAllHtmlPagesAndUpdate();
        })->everyMinute();
        //check for requests and send emails
        $schedule->call(function(){
            //sleep for 30 secs
            //sleep(30);
            //then do the job
            Request::notifyOpenCourses();
        })->everyMinute();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
