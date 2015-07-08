<?php namespace System;


abstract class Controller
{

    protected $view;
    public $response;

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
            $currentRoute = Route::getCurrentRoute();
            echo $this->view->render($currentRoute[1][0] . '/' . $currentRoute[1][1]);
        }
    }

}
