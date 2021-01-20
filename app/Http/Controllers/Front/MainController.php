<?php

namespace App\Http\Controllers\Front;

use Laravel\Lumen\Routing\Controller as BaseController;


/**
 * Class MainController
 * @package App\Http\Controllers\Front
 */
class MainController extends BaseController
{


    /**
     * @var array
     */
    public $data = [];


    /**
     * @var string
     */
    public $rootViewFolder;



    public $locale = null;


    /**
     * MainController constructor.
     */
    public function __construct()
    {
        $this->locale = app()->getLocale();

        $this->rootViewFolder = "front";
    }


    /**
     * @param array $data
     * @return mixed
     */
    public function render($view, $data = [])
    {
        $data['locale'] = $this->locale;

        $this->data = array_merge($this->data, $data);

        return view($view, $this->data);
    }


    /**
     * @param $data
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function json($data)
    {
        return response(array_merge($this->data, $data));
    }


}