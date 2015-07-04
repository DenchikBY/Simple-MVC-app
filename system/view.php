<?php namespace System;


class View
{

    private $viewsPath = APP_PATH . '/app/views';
    public $layout = 'main';
    private $data = [];

    public function render($viewName)
    {
        $viewPath = realpath($this->viewsPath . '/' . $viewName . '.php');
        $layoutPath = realpath($this->viewsPath . '/layouts/' . $this->layout . '.php');
        $this->share('content', $this->renderFile($viewPath));
        return $this->renderFile($layoutPath);
    }

    private function renderFile($path)
    {
        extract($this->data);
        ob_start();
        include $path;
        return ob_get_clean();
    }

    public function share($key, $value)
    {
        if (gettype($key) == 'array') {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
    }

}
