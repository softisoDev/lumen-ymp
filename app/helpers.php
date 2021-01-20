<?php

if (!function_exists('auth')) {
    /**
     * @return \Illuminate\Auth\AuthManager
     */
    function auth()
    {
        return app('auth');
    }
}

if (!function_exists('request')) {
    /**
     * @return \Laravel\Lumen\Http\Request
     */
    function request()
    {
        return app('request');
    }
}

if (!function_exists('apiResource')) {
    function apiResource(\Laravel\Lumen\Routing\Router $router, $path, $controller, $params = [])
    {
        $parameter = $params[$path] ?? $path;
        $actions = [
            'index'   => [
                'method' => 'GET',
                'path'   => '/'
            ],
            'search'  => [
                'method' => 'GET',
                'path'   => '/search/{query}'
            ],
            'store'   => [
                'method' => 'POST',
                'path'   => '/',
            ],
            'show'    => [
                'method' => 'GET',
                'path'   => '/{' . $parameter . ':[0-9]+}',
            ],
            'update'  => [
                'method' => 'PUT',
                'path'   => '/{' . $parameter . ':[0-9]+}',
            ],
            'destroy' => [
                'method' => 'DELETE',
                'path'   => '/{' . $parameter . ':[0-9]+}',
            ],
        ];
        if (isset($params['only'])) {
            $actions = \Illuminate\Support\Arr::only($actions, $params['only']);
        }
        if (isset($params['except'])) {
            $actions = \Illuminate\Support\Arr::except($actions, $params['except']);
        }
        foreach ($actions as $action => $actionOptions) {
            $router->addRoute($actionOptions['method'], rtrim($path, '/') . $actionOptions['path'], $controller . '@' . $action);
        }
    }
}


if (!function_exists('config_path')) {
    /**
     * @param string $path
     * @return string
     */
    function config_path($path = '')
    {
        return app()->configPath($path);
    }
}


if (!function_exists('youtube')) {
    /**
     * @return \App\Library\Youtube
     */
    function youtube($apiKey = null)
    {
        return $apiKey
            ? app('youtube')->setKey($apiKey)
            : app('youtube');
    }
}

if (!function_exists('audio')) {
    /**
     * @param string $videoId
     * @return \App\Library\Audio
     */
    function audio($videoId = null)
    {
        return \App\Library\Audio::id($videoId);
    }
}


if (!function_exists('youtube_dl')) {
    /**
     * @param string $url
     * @return \App\Library\YoutubeDL
     */
    function youtube_dl($url = null)
    {
        return \App\Library\YoutubeDL::url($url);
    }
}



/**
 * @param string $path
 * @return string
 */
function clear_string($str)
{
    $str = html_entity_decode($str);
    $str = htmlspecialchars_decode($str, ENT_QUOTES);

    $str = preg_replace('/(\v|\s)+/', ' ', $str);

    return $str;
}

/**
 * @param string $path
 * @return string
 */
function clear_string2($str)
{
    $str = html_entity_decode($str);

    $str = preg_replace('/(\v|\s)+/', ' ', $str);

    return $str;
}