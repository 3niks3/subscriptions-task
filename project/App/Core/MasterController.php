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

    public function view($view,$data=[])
    {
        $data['_request'] =$this->request;

        if(count($data))
        {
            extract($data);
        }

        $root = $_SERVER['DOCUMENT_ROOT'];
        require_once $root.'/views/'.$view.'.php';
    }
}