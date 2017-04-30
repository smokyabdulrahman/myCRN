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
                '__VIEWSTATE' => 'KoyumFMzmsuF7dLSnxpltboQbDHIijULT66r1FROTvEGAqjWuRbyTEaiSCrBIdj58h5mOhJYohPNalrUDyiqGD5xYp7HV6UX7wcUkCzlFY2l0e35L60YazKeuHMZ0JtO9WlB4UXpZTtycFmUqMPltQ==',
                '__VIEWSTATEGENERATOR' => '0AFBBFE1',
                '__EVENTVALIDATION' => 'QCGCYTsZ2MP8xVXEvS2zzQb51IDmS2gTBpMPu7HJg1JUPrOpigZJPuLH0HMDna8UPcVCRyzWVpXtBGGK+zMbFCmeQ5zihKlg79AU7P0GYxhwA7ywpI/bIdsdGCk3x45a2GAS0wlq9jNrpXTmXlBn7zO+hP46IvdxIzQu2YQbOTv75sPY1Pw7ogt4DJvrO5TWgIxs6Yt/nrNkbwr/Xd2Gp2MpOnr8UrNo+Iai87idy75E+DIzEOBPxtiHfjcIBqfpeTC9RA31OUS2f80khS68zoHNjD+VkTT5BGv9iEivJN3G6a5RrHO2nNgGtza+xXY52l7Xb8WlPlMk/2ntO0Ace8kw+M+HKoXduVLNR12g3hjibgFUsIHWySTCdQz5vv3fPeis4kZc5xfjB13jh9IMLsfC8DllrUe+yR9B0DEpJ2sJhZgcbT0hDbJzwziN4zih5DKE5HnxqEUjfYvfVv4T+MH52QrhweoyO3fdYaXR8ea6KNhrxTBeZ1rC9NR8t//qMcMNqtB1eb3EtdIkA9d/qhdL4hiMBH2BFqgFDdgZXHm5RWlpBYTQtB6q/E0emSsml4+/D71anPev900nypWThq9jqrTHmOor/dqYKA+iEXrF7QImUQ1Nm5xqv4Y63djHlSpnEgzsXv3TnvWSjB0kBaIjmigsuE9XauvYdcBX7dYrhSMSsY2MDNWyiAIq+Osl+UOESqLZdfs0go3eotsNr3h9YcSTP7fkgR6jhLH/b5OZPBGJ/z+dd2R3X5LYg7pe0XO+5rRKHzGzDQBUuczT9SJFCcBCKSeCV4WqJthPxuc4dw/IN/Kq5T6CtigYg/lWe0gioYQxzg4sxCcaSQNlXa46ZpUp+jGLZqYJ2ppX13Mzyujl5Wibjfkj0QaB58/x',
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