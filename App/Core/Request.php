<?php


namespace App\Core;


class Request
{
    public $method;
    public $post_data;
    public $get_data;
    public $session_data;
    public $session_flush_data;
    public $is_ajax;

    public function __construct($method)
    {
        $this->method = $method;
        $this->post_data = $_POST;
        $this->get_data = $_GET;
        $this->is_ajax = false;

        //update sessions
        $session_flush_data = $_SESSION['flush']??[];
        unset($_SESSION['flush']);

        $this->session_data =  $_SESSION;
        $this->session_flush_data = $session_flush_data;

        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $this->is_ajax = true;
        }
    }

    public function get($scope, $key)
    {
        switch(true) {
            case($scope == 'post'):
                return $this->post_data[$key]??null;
                break;
            case($scope == 'get'):
                return $this->get_data[$key]??null;
                break;
            case($scope == 'session'):
                return $this->session_data[$key]??null;
                break;
            case($scope == 'flush'):
                return $this->session_flush_data[$key]??null;
                break;
            default:
                return null;
                break;
        }
    }

    public function set($scope, $key, $value)
    {
        switch(true) {
            case($scope == 'post'):
                $_POST[$key] = $value;
                $this->post_data[$key] = $value;
                break;
            case($scope == 'get'):
                $_GET[$key] = $value;
                $this->get_data[$key] = $value;
                break;
            case($scope == 'session'):
                $_SESSION[$key] = $value;
                $this->session_data[$key] = $value;;
                break;
            case($scope == 'flush'):
                $_SESSION['flush'][$key] = $value;
                $this->session_flush_data[$key] = $value;
                break;
        }

        return true;
    }

    public function exists($scope, $key)
    {
        switch(true) {
            case($scope == 'post'):
                return isset($this->post_data[$key]);
                break;
            case($scope == 'get'):
                return isset($this->get_data[$key]);
                break;
            case($scope == 'session'):
                return isset($this->session_data[$key]);
                break;
            case($scope == 'flush'):
                return isset($this->session_flush_data[$key]);
                break;
            default:
                return false;
                break;
        }
    }

    public function isAjax()
    {
        return $this->is_ajax;
    }
}