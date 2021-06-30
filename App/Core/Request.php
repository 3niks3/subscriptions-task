<?php


namespace App\Core;


class Request
{
    public $method;
    public $post_data;
    public $get_data;

    public function __construct($method)
    {
        $this->method = $method;
        $this->post_data = $_POST;
        $this->get_data = $_GET;
    }
}