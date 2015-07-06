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
            if (gettype($route) == 'integer') {
                $route = $action;
            }
            $regEx = self::routeToRegEx($route);
            if (preg_match($regEx[0], $_SERVER['REQUEST_URI'], $matches)) {
                self::$currentRoute = [$route, explode('@', $action)];
                $params = array_slice($matches, 1);
                for ($i = 0; $i < 2; ++$i) {
                    $pos = $regEx[1][$i];
                    if ($pos !== null) {
                        self::$currentRoute[1][$i] = $params[$pos];
                        unset($params[$pos]);
                    }
                }
                $params = array_values($params);
                self::startRoute($params);
                break;
            }
        }
        if (!self::$currentRoute) {
            self::error404();
        }
    }

    private static function startRoute(array &$params)
    {
        $controllerName = 'App\\Controllers\\' . ucfirst(self::$currentRoute[1][0]);
        $actionName = self::$currentRoute[1][1];
        $controller = new $controllerName;
        $controller->before();
        $controller->response = self::startControllerAction($controller, $actionName, $params);
        $controller->after();
        DB::closeConnection();
        if (Config::get('short_response') == true) {
            $response = preg_replace([
                '/<!--([^\[|(<!)].*)/',
                '/(?<!\S)\/\/\s*[^\r\n]*/',
                '/\s{2,}/',
                '/(\r?\n)/'
            ], '', ob_get_contents());
            ob_clean();
            echo $response;
        }
    }

    private static function routeToRegEx($route)
    {
        $actionParams = [null, null];
        $counter = 0;
        $regEx = preg_replace_callback('/{.+}/U', function ($matches) use (&$counter, &$actionParams) {
            if ($matches[0] == '{controller}') {
                $actionParams[0] = $counter;
            } elseif ($matches[0] == '{action}') {
                $actionParams[1] = $counter;
            }
            ++$counter;
            return '([0-9A-Za-z\-]+)';
        }, $route);
        $regEx = str_replace('/', '\/', $regEx);
        $regEx = '/' . $regEx . '$/';
        return [$regEx, $actionParams];
    }

    private static function startControllerAction(&$controller, &$actionName, array &$params = [])
    {
        $count = count($params);
        if (phpversion() >= 5.6) {
            return eval('$controller->$actionName(...$params);');
        } else {
            if ($count == 0) {
                return $controller->$actionName();
            } else if ($count == 1) {
                return $controller->$actionName($params[0]);
            } else if ($count == 2) {
                return $controller->$actionName($params[0], $params[1]);
            } else if ($count == 3) {
                return $controller->$actionName($params[0], $params[1], $params[2]);
            } else if ($count == 4) {
                return $controller->$actionName($params[0], $params[1], $params[2], $params[3]);
            } else {
                return $controller->$actionName($params[0], $params[1], $params[2], $params[3], $params[4]);
            }
        }
    }

    public function error404()
    {
        header('HTTP/1.0 404 Not Found - Archive Empty');
        echo '404 Not Found';
        exit;
    }

}
