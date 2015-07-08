<?php namespace App\Controllers;

use App\Models\Topic;
use System\Controller;
use System\DB;

class Index extends Controller
{

    public function index()
    {
        /*
        $topic = new Topic();
        $topic->title = 'title';
        $topic->text = 'text';
        $topic->save();
        var_dump($topic->id);
        $topic->update(['title' => '123', 'text' => 'blalalalala']);
        */
        //var_dump(Topic::select('where id > 20 order by id desc limit 5'));
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
