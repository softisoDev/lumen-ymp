<?php

namespace App\Http\Controllers\Front;


use Illuminate\Support\Facades\Request;


/**
 * Class HomeController
 * @package App\Http\Controllers\Front
 */
class HomeController extends MainController
{



    /**
     * HomeController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @return mixed
     */
    public function index()
    {
        return $this->render("www.home.index");
    }



    public function watch()
    {
        return self::index();
    }


    /**
     * @return \Illuminate\View\View
     */
    public function getPage($page)
    {
        switch ($page) {
            case 'about':
                $content = __('pages.about');
                break;
            case 'privacy-policy':
                $content = __('pages.privacy-policy');
                break;
            case 'term-of-service':
                $content = __('pages.term-of-service');
                break;
            default:
                $content = '404 not found';
                break;
        }

        return $this->render('www.page.index', ['content' => $content]);
    }



    public function showError()
    {
        switch (Request::segment(2)) {
            case 'long-video':
                $message = __('messages.long_video');
                break;
            case 'oops':
            default:
                $message = __('messages.general_error_msg');
                break;
        }

        return $this->render('www.errors.index', ['message' => $message]);
    }

    public function test()
    {
        return redirect(url('error/long-video'));

    }

}
