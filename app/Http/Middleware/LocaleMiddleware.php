<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Request;

/**
 * Class SetLocale
 * @package App\Http\Middleware
 */
class LocaleMiddleware
{


    public static $mainLanguage = 'en'; //основной язык, который не должен отображаться в URl

    public static $languages = ['en', 'ru', 'tr']; // Указываем, какие языки будем использовать в приложении.



    /*
     * Проверяет наличие корректной метки языка в текущем URL
     * Возвращает метку или значеие null, если нет метки
     */
    public static function getLocale()
    {
        $uri = $_SERVER['REQUEST_URI']??null; //получаем URI
        $uri = trim($uri, '/');

        $segmentsURI = explode('/', $uri); //делим на части по разделителю "/"

        //Проверяем метку языка  - есть ли она среди доступных языков
        if (!empty($segmentsURI[0]) && in_array($segmentsURI[0], self::$languages)) {

            if ($segmentsURI[0] != self::$mainLanguage) return $segmentsURI[0];

        }

        return self::$mainLanguage;
    }




    /*
    * Устанавливает язык приложения в зависимости от метки языка из URL
    */
    public function handle($request, Closure $next)
    {
        $locale = self::getLocale();

        if($locale) app()->setLocale($locale);
        //если метки нет - устанавливаем основной язык $mainLanguage
        else app()->setLocale(self::$mainLanguage);

        return $next($request); //пропускаем дальше - передаем в следующий посредник
    }

}
