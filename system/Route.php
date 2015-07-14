<?php namespace System;

class Route
{

    private static $routes;
    private static $currentRoute;
    private static $baseUrl;
    private static $requestUrl;

    public static function init()
    {
        $routesPath = realpath(APP_PATH . '/routes.php');
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
        self::$baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . $base;
        self::$requestUrl = substr($_SERVER['REQUEST_URI'], strlen($base));
        if (self::$requestUrl == '' || self::$requestUrl == '//') {
            self::$requestUrl = '/';
        } else if (self::$requestUrl != '/') {
        	self::$requestUrl = ltrim(self::$requestUrl, '/');
        }
    }

    private static function findRoute()
    {
        foreach (self::$routes as $route => $action) {
            if (gettype($route) == 'integer') {
                $route = $action;
            }
            $regEx = self::routeToRegEx($route);
            if (preg_match($regEx[0], self::$requestUrl, $matches)) {
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
        $controller->response = call_user_func_array([$controller, $actionName], $params);
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
        $regEx = preg_replace_callback('/{(\w+)(?:[:](.+))?(\?)?}/U', function ($matches) use (&$counter, &$actionParams, &$route) {
            $regEx = '([\d\w\-]+)';
            $paramName = $matches[1];
            if ($paramName == 'controller') {
                $actionParams[0] = $counter;
            } elseif ($paramName == 'action') {
                $actionParams[1] = $counter;
            }
            $qKey = array_search('?', $matches);
            $rKey = ($qKey == 3 || !$qKey) ? 2 : null;
            if ($rKey && isset($matches[$rKey]) && strlen($matches[$rKey]) > 0) {
                $pReg = &$matches[$rKey];
                $regEx = $pReg;
                if ($pReg[0] != '(') $regEx = '(' . $regEx;
                if (substr($pReg, -1) != ')') $regEx .= ')';
            }
            if ($qKey > 0) {
                $regEx .= '?';
                if (strpos($route, $paramName) > 1) {
                    $regEx = '?' . $regEx;
                }
            }
            ++$counter;
            return $regEx;
        }, $route);
        $regEx = str_replace('/', '\/', $regEx);
        $regEx = '/^' . $regEx . '$/';
        return [$regEx, $actionParams];
    }

    public static function error404()
    {
        header('HTTP/1.0 404 Not Found - Archive Empty');
        echo '404 Not Found';
        exit;
    }

    public static function getBaseUrl()
    {
        return self::$baseUrl;
    }

    public static function getCurrentRoute()
    {
        return self::$currentRoute;
    }

}
