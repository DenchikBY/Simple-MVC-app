<?php namespace System;


class Controller
{

    protected $view;
    public $response;
    public $route;

    public function __construct()
    {
        $this->view = new View();
    }

    public function before()
    {
    }

    public function after()
    {
        if (strlen($this->response) > 0) {
            echo $this->response;
        } else {
            echo $this->view->render($this->route[0] . '/' . $this->route[1]);
        }
    }

}
