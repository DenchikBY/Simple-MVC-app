<?php namespace System;

class Route
{

    private static $routes;

    public static function init()
    {
        $routesPath = realpath(__DIR__ . '/../app/routes.php');
        if (file_exists($routesPath)) {
            self::$routes = include $routesPath;

            self::getRequestUrl();
            self::findRoute();

        } else {
            throw new \Exception('Routes file not exists:' . $routesPath);
        }
    }

    private static function getRequestUrl()
    {
        $base = dirname($_SERVER['PHP_SELF']);
        if (ltrim($base, '/')) {
            $_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], strlen($base));
        }
        return $_SERVER['REQUEST_URI'];
    }

    private static function findRoute()
    {
        foreach (self::$routes as $route => $action) {
            $regEx = preg_quote($route);
            $regEx = str_replace('/', '\/', $regEx);
            $regEx = '/' . $regEx . '$/';
            if (preg_match($regEx, $_SERVER['REQUEST_URI'])) {
                self::startRoute($action);
                break;
            }
        }
    }

    private static function startRoute($action)
    {
        $action = explode('@', $action);
        $controllerName = 'App\\Controllers\\' . ucfirst($action[0]);
        $actionName = $action[1];
        $controller = new $controllerName;
        $controller->route = $action;
        $controller->before();
        $controller->response = $controller->$actionName();
        $controller->after();
    }

}
