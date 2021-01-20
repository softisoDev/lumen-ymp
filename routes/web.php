<?php

/*$router->group(['prefix' => '{locale}', 'where' => ['locale' => '[a-zA-Z]{2}']], function () use ($router) {
    //$router->post('search', ['as' => 'search', 'uses' => 'Front\SearchController@index']);
    $router->get('/', ['as' => 'home', 'uses' => 'Front\HomeController@index']);
});*/

/**
 * @var Laravel\Lumen\Routing\Router $router
 */

$router->get('/get/{videoId}', ['as' => 'get/{id}', 'uses' => 'Front\DownloadController@getFile']);

$router->group(['prefix' => \App\Http\Middleware\LocaleMiddleware::getLocale()], function () use ($router) {
    $router->post('search', ['as' => 'search', 'uses' => 'Front\SearchController@index']);
    $router->get('/watch', ['uses' => 'Front\HomeController@watch']);
    $router->get('/error/{type}', ['as' => '{type}', 'uses' => 'Front\HomeController@showError', 'type' => 'oops|long-video']);
    $router->get('/{page}', ['uses' => 'Front\HomeController@getPage', 'page' => 'about|term-of-service|privacy-policy|not-found|long-video']);
    $router->get('/', ['as' => 'home', 'uses' => 'Front\HomeController@index']);
});

$router->get('/develop', ['uses' => 'Front\DevelopController@index']);
$router->post('search', ['as' => 'search', 'uses' => 'Front\SearchController@index']);
$router->get('/watch', ['uses' => 'Front\HomeController@watch']);
$router->get('/error/{type}', ['as' => '{type}', 'uses' => 'Front\HomeController@showError', 'type' => 'oops|long-video']);
$router->get('/{page}', ['uses' => 'Front\HomeController@getPage', 'page' => 'about|term-of-service|privacy-policy|not-found|long-video']);
$router->get('/', ['as' => 'home', 'uses' => 'Front\HomeController@index']);