<?php namespace System;


class View
{

    public $layout = 'main';
    private $data = [];

    public function render($viewName)
    {
        $viewsPath = APP_PATH . '/app/views';
        $viewPath = realpath($viewsPath . '/' . $viewName . '.php');
        $layoutPath = realpath($viewsPath . '/layouts/' . $this->layout . '.php');
        $this->share('content', $viewPath ? $this->renderFile($viewPath) : '');
        return $this->renderFile($layoutPath);
    }

    private function renderFile($path)
    {
        extract($this->data);
        ob_start();
        include $path;
        return ob_get_clean();
    }

    public function share($key, $value = null)
    {
        if (gettype($key) == 'array') {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
    }

}
