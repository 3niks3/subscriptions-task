<?php
namespace App\Core;

use App\Controllers\Controller;

class Router{

    static public function setup()
    {
        $url = trim(strtok($_SERVER["REQUEST_URI"], '?'),'/\\');
        $url = trim($url);
        $method = (!isset($url) || empty($url))?'index':$url;


        if(!method_exists('App\Controllers\Controller', $method)){
            header("HTTP/1.0 404 Not Found");
            echo "<h1>404 Not Found</h1>";
            echo "The page that you have requested could not be found.  <a href='/'>Home link</a>";
            exit();
        }

        $request = new Request($method);
        $controller = new Controller($request, $method, $url);

        call_user_func_array([$controller,$method],[]);
    }
}