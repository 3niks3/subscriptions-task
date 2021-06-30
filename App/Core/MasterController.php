<?php


namespace App\Core;


class MasterController
{
    public $request;
    public $method;
    public $url;

    public function __construct($request, $method, $url)
    {
        $this->request = $request;
        $this->method = $method;
        $this->url = $url;
    }

    public function view($view,$data=[]){
//        foreach($data as $variable_name => $variable_value)
//        {
//            ${$variable_name} = $variable_value;
//        }

        if(count($data))
        {
            extract($data);
        }

        $root = $_SERVER['DOCUMENT_ROOT'];
        require_once $root.'/App/views/'.$view.'.php';

    }
}