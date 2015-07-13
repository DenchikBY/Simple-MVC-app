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
        //var_dump(DB::table('themes')->where('id', 40)->get());
        //var_dump(Topic::offset(10)->limit(5)->orderBy('id', 'desc')->get());
        //var_dump(Topic::where('id', '>', 25)->last());
        /*
        var_dump(DB::table('themes')->insert([
            ['title' => '123', 'text' => 'blalalalala'],
            ['title' => '123', 'text' => 'blalalalala']
        ]));
        var_dump(Topic::insert(['title' => '123', 'text' => 'blalalalala']));
        var_dump(Topic::insert([
            ['title' => '123', 'text' => 'blalalalala'],
            ['title' => '123', 'text' => 'blalalalala']
        ]));
        */
        //Topic::last()->save(['text' => 222]);
        //Topic::update(['text' => '777']);
        //DB::table('themes')->update(['text' => '888']);
        //Topic::where('id', 54)->update(['text' => 1001]);
        //Topic::save(['title' => '123', 'text' => 'blalalalala']);
        //Topic::where('id', '<', 8)->delete();
        //Topic::where('text', 222)->decrement('counter');

        //var_dump(DB::getLastQuery());

        $this->view->share('name', 'Denchik');
    }

    public function admin($action = 'index', $page = 5)
    {
        var_dump($action, $page);
    }

    public function page($id)
    {
        echo $id;
    }

    public function post($postId, $commentId)
    {
        var_dump($postId, $commentId);
    }

}
