<?php namespace System;

class Route
{

    private static $routes;
    public static $currentRoute;

    public static function init()
    {
        $routesPath = realpath(APP_PATH . '/app/routes.php');
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
            $regEx = str_replace('/', '\/', $route);
            $regEx = '/' . $regEx . '$/';
            if (preg_match($regEx, $_SERVER['REQUEST_URI'], $matches)) {
                self::$currentRoute = [$route, explode('@', $action)];
                self::startRoute(array_slice($matches, 1));
                break;
            }
        }
        if (!self::$currentRoute) {
            self::error404();
        }
    }

    private static function startRoute($params)
    {
        $controllerName = 'App\\Controllers\\' . ucfirst(self::$currentRoute[1][0]);
        $actionName = self::$currentRoute[1][1];
        $controller = new $controllerName;
        $controller->before();
        $controller->response = $controller->$actionName(...$params);
        $controller->after();
    }

    public function error404()
    {
        header('HTTP/1.0 404 Not Found - Archive Empty');
        echo '404 Not Found';
        exit;
    }

}
