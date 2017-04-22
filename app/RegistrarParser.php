<?php
/**
 * Created by PhpStorm.
 * User: smokyabdulrahman
 * Date: 3/31/2017
 * Time: 11:09 AM
 */

namespace App;


use Sunra\PhpSimple\HtmlDomParser;
use GuzzleHttp\Client;
class RegistrarParser
{
    //class constants
    static $departments = ["ACCT","AE","ARC","ARE","CE"
    ,"CEM","CHE","CHEM","COE","CRP",
    "EE",
    "ELD","ERTH","ELI","FIN","GS","IAS","ICS",
    "MATH","ME","MGT","PE","PETE",
    "PHYS","PSE","SE"];

    static $term = "201710";

    public function getAllHtmlPagesAndBuild(){

        $time_start = microtime(true);

        $html = [];
        foreach(RegistrarParser::$departments as $department){
            $this->buildCoursesTable($this->getHtmlPage(RegistrarParser::$term, $department));
        }

        $time_end = microtime(true);
        $time = $time_end - $time_start;

        echo "<br><b>$time</b>";

        return $html;
    }

    public function getAllHtmlPagesAndUpdate(){

        $time_start = microtime(true);

        $html = [];
        foreach(RegistrarParser::$departments as $department){
            $this->updateCourses($this->getHtmlPage(RegistrarParser::$term, $department));
        }

        $time_end = microtime(true);
        $time = $time_end - $time_start;

        echo "<br><b>$time</b>";

        return $html;
    }

    public function getHtmlPage($term, $major){
        $client = new Client(['base_uri' => 'http://registrar.kfupm.edu.sa/CourseOffering']);
        $response = $client->request('POST', '',[
            'form_params' => [
                '__EVENTTARGET' => 'ctl00$CntntPlcHldr$ddlDept',
                '__EVENTARGUMENT' => '',
                '__LASTFOCUS' => '',
                '__VIEWSTATE' => 'Riqbe06pw2Oy7plngEThHDnFboT3N4w8I9hpN0EG2a0U7PGVXizdngjCZR91XN1aXN5dBNLG6xN35UOOsgSe9ai+pLj33+AefDQsZBOlVkQTjvVfsM/O6e9vJvuw80HSrl8hPD4Q38tDINrCpJjHWg==',
                '__VIEWSTATEGENERATOR' => '0AFBBFE1',
                '__EVENTVALIDATION' => 'NGR7oCt2y/JlQdBHn7c0GqneQGjMm8UBb7pOAwBwwmc8xVLtFRyJ6aJzTKXOK4TawnPQeXCBxbPvRu8eiGwLEdhAyLFKE4RAhNDAuz2XL0CNORXQrocw7hGTxJHhqcfarAZcnTwQCYSbGtUzk1rJgZ0xsCfs3jBKHa9TtMXyioO3zjXuQ3DGPN2j9LPjw1rEq/6/K0+F+auZo0LA0wHoKG+RxERmpNtAbjDIWgASZVp5EraxZ5q0lPhu6kUQCEoQs29et90oMfZIdll0hULVW7i9qx2zK86Op6akgT9ZiLwspV0IpsgUdAuiwnIwZLMqrXSmSJ+G5ogeAvCgVkK76Q87TovfNbNLc3WB1kqzJOes9FJjfMGJkZgSwkz/a9iZXDssMHhM3RELkkJCweIU364xu+5W/K5QUEwyDRyvUKucBXTLt32E76tHIn2cb2VVp2MRbrd5+rkd4Sh5BVNpEG2peLpgE6ErM0LGT+jKA54G/zP9phJ6MvKBA4nXfmxlPBISy5xzwwCHxSSZU/rmn+ARQXzpiz4yBykgBGezPPz5kmz2Shm46w43N5mgtln6TsjlU9jjFx9Oh1tmb8sjb88fgojd64D42M3QECcOnUte/gUgML1c0Iq0VotHIK4qXKLA9lu0Qb3jc+l3tGHhgGR0N1NU1kkJC6dQiMZVQPElg8bG0YMeK6lbW/bHvcoXRzX2LWhvQ8ouqzXIJwmxryTcvEs5L9LfMgjm9TDRZGX8XM2Fw0xrblkH6Y1PHW8iM48YJ/fDqK8HEXk+qIwFJcbMLR4lOr+sNPwKWFmVzQ4P7AjneUZpKQGLO8EJfJOXzlfgVyx+3FzCdpTQGi6cO99BcUbfqnhwcBq2d8HMfJ+tnnVSIJLrnL4ScmLT31YX',
                'ctl00$CntntPlcHldr$ddlTerm' => $term,
                'ctl00$CntntPlcHldr$ddlDept' => $major
            ]
        ]);

        //$context = stream_context_create($response);

        return HtmlDomParser::str_get_html($response->getBody());
    }

    public function updateCourses($html){

        $prevCrn = "0";
        $counter = 0;

        foreach( $html->find('.trow') as $section){
            $keys = array("SEC", "ACTIVITY", "CRN", "NAME", "INSTRUCTOR", "DAY", "TIME", "LOCATION", "STATUS");


            $courseArr = array();

            //counter
            $i = 0;
            foreach($section->find('.tdata') as $attr){
                //split all by ':'
                $info = explode(":", $attr->plaintext);

                $courseArr[$keys[$i]] = trim($info[1]);

                $i++;
            }

            if($prevCrn == $courseArr["CRN"] && $counter > 0)
                continue;

            //get course
            $course = Course::where('crn', '=', (integer)$courseArr["CRN"])->first();

            $changedStatus = false;

            //replace open by 1, and closed by 0
            $status = -1;
            if($courseArr["STATUS"] == "Open")
                $status = 1;
            else
                $status = 0;

            if($course->status != $status){
                $course->status = $status;
                $changedStatus = true;
            }

            if($changedStatus){
                try {
                    $course->save();

                } catch (\Illuminate\Database\QueryException $e) {
                    dd($e);
                }
            }

            $prevCrn = $courseArr["CRN"];
            $counter++;

        }

    }

    public function buildCoursesTable($html){
        $prevCrn = "0";
        $counter = 0;
        foreach( $html->find('.trow') as $section){
            $keys = array("SEC", "ACTIVITY", "CRN", "NAME", "INSTRUCTOR", "DAY", "TIME", "LOCATION", "STATUS");


            $courseArr = array();

            //counter
            $i = 0;
            foreach($section->find('.tdata') as $attr){
                //split all by ':'
                $info = explode(":", $attr->plaintext);

                $courseArr[$keys[$i]] = trim($info[1]);

                $i++;
            }

            if($prevCrn == $courseArr["CRN"] && $counter > 0)
                continue;

//            if($courseArr["ACTIVITY"] == "LAB")
//                continue;

            $course = new Course();
            if($courseArr["STATUS"] == "Open")
                $course->status = 1;
            else
                $course->status = 0;
            $course->name = $courseArr["NAME"];
            $course->time = $courseArr["TIME"];
            $course->days = $courseArr["DAY"];
            $course->crn = (integer)$courseArr["CRN"];
            try {
                $course->save();

            } catch (\Illuminate\Database\QueryException $e) {
                dd($e);
            }
            $prevCrn = $courseArr["CRN"];
            $counter++;
        }
    }
}