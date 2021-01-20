<?php

namespace App\Http\Controllers\Front;


use App\Helpers\SearchHelper;
use App\Library\Youtube;
use App\Library\YoutubeDetail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;


/**
 * Class HomeController
 * @package App\Http\Controllers\Front
 */
class DevelopController extends MainController
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
        if ( isset($_GET['module']) )
        {
            if ( $_GET['module'] == 'ips' )
            {
                return $this->ips();
            }
        }

        dd(audio()->search('xpert'), 'test');



//        $y = new Youtube();
//
//        $r = $y->searchVideo('eminem', 5);
//
//        dd($r);
//
//        exit;
//
//
//        $search = new SearchHelper();
//
//        $response = $search->searchByQuery('eminem');
//
//        dd($response);

//
//        $response = $search->searchByQuery('xpert');
//
//        dd($response);

    }




    public function ips()
    {
        $keys = Redis::connection('cache')->keys('LOG*');

        $data = [];

        Cache::getStore()->setPrefix('LOG');

        foreach ($keys as $key)
        {
            $value = Cache::get(str_replace('LOG:', '', $key));

            $data[$value][] = str_replace('LOG:banned:', '', $key);

            if ( $value > 200 )
            {
                Cache::forget(str_replace('LOG:', '', $key));
            }
        }

        ksort($data);

        foreach ($data as $k => $v)
        {
            if ( $k > 200 )
            {
                foreach ($v as $ip)
                {
                    echo "deny $ip;<br>";
                }
            }
        }

        dd($data);
    }



}
