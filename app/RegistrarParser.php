<?php
/**
 * Created by PhpStorm.
 * User: smokyabdulrahman
 * Date: 3/31/2017
 * Time: 11:09 AM
 */

namespace App;


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

    static $term = "201620";

    public function getAllHtmlPages(){
        $html = [];
        foreach(RegistrarParser::$departments as $department){
            $html[] = $this->getHtmlPage(RegistrarParser::$term, $department);
        }
        return $html;
    }

    public function getHtmlPage($term, $major){
        $client = new Client(['base_uri' => 'http://registrar.kfupm.edu.sa/CourseOffering']);
        $response = $client->request('POST', '',[
            'form_params' => [
                '__EVENTTARGET' => 'ctl00$CntntPlcHldr$ddlDept',
                '__EVENTARGUMENT' => '',
                '__LASTFOCUS' => '',
                '__VIEWSTATE' => 'hhfr7EY+cHf/jFNZSbXyvAuks+WklVUCSME7AMQyo3emYCFOvXKl+8Aee4RBkaQZ54Gpg2vUpElGmNw5vJglzLX6izqTVaWd8a4MntrTF8DX8XQ+raz412Is1BI6cNkxvxbnHDIKUDEwFlEOCDDFaQ==',
                '__VIEWSTATEGENERATOR' => '0AFBBFE1',
                '__EVENTVALIDATION' => '+UFxprmMbaW3zhdg+NPVdVhLmAzfx0MVfAHCjTiflHsqEM2pK5ZUBJF9fYRievgrIIfUN9zwuSmnMwpshe8bcTa1p1k651Kh78RS2dczGdrwuXXAApGHsxMjENtTJjxQyvm5hM/XfEF8R2PfQzFdoEsMsdp6j//mq9nZi0BFYMuiyqFbBQkQAicUi6ykNkW7rKMH+0THSnEjWOX+i2F/gDueqj3J4KlXVcW3lF6AbNFKDwWMxpUZJ7tOXYq7PPfcXaWsgx7qQVUWyFvhbpWiCiVdkfEsjPAs/FcukzJPGqKN2KGdXo0Tg3bKAa2J0AYJu9XRMW+W3PTIl9xr/74xi4iSnim/p2y4bLUBLjkrSdNsCchcGFDXQvMo9EOCNtgJQG+uqcyKTLCC0aSsO0LD/IQV7hLLFMCWhQbNTczRwtkjxUWB7SdhSYp3utWWhgP6Z5XvQb6yyugddxeqlKhxRg3QoiM5Wp9raEnDzVARMA/K9scR6m68KGhEK1wMuCoYeKHt+WoSURuYT2A2iHu+nKkYAgDZRF8M6HuD8IhwDwNjbDrYUCIrndEsRILsWlMeWr/6HZagJImJCKybmJtomx7107Uc0y6ibCadBwbl2AyuDp4Bp8rA1BvAWo2liZF1oIlo3KuQDSV7eWCavakp8Okgof4H0ytc3CvVJ4w/HXdy+icOR4CVAQXP+YJ10Z01MNuOcX1m8KNvIYMY4Za4gSW7hwpHMdEewYBFkmuudbOyMzDPLwBnY9iFeixHGrog4ZBygQcPTixwzk5YVPT91xfnmFCXlsGLMrFH9yaK4X72xZigUXW7jJbkkgst2MoepsBwibwbtPUeoQ8tGBvYKFO6vPHpd2c41bpW4FnhNO0=',
                'ctl00$CntntPlcHldr$ddlTerm' => $term,
                'ctl00$CntntPlcHldr$ddlDept' => $major
            ]
        ]);

        //$context = stream_context_create($response);

        return $response->getBody()->getContents();
    }

    public function update(){

    }
}