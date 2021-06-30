<?php
namespace App\Controllers;

use App\Core\MasterController;

class Controller extends MasterController
{

    public function index()
    {
        $tests = '123';
        $home = 'home';
        $this->view('subscribe',compact(['tests', 'home']));

    }
    public function testMethod()
    {
        die('end testMethod');
    }
}