<?php namespace App\Controllers;

use System\Controller;
use System\Db;

class Index extends Controller
{

    public function index()
    {
        //echo 123;
        //return $this->view->render('index/index');
        Db::getConnection();
        $this->view->share('name', 'Denchik');
    }

    public function admin($action, $page)
    {
        var_dump($action, $page);
    }

    public function page($id)
    {
        echo $id;
    }

}
