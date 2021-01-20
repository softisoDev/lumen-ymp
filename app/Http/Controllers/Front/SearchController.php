<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Helpers\SearchHelper;

/**
 * Class SearchController
 * @package App\Http\Controllers\Front
 */
class SearchController extends MainController
{


    /**
     * @var null
     */
    public $limit = null;


    /**
     * SearchController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function index(Request $request)
    {
        $response = [
            'status' => false,
            'message' => null,
            'errors' => [],
            'data' => [
                'type' => 'html',
                'content' => null,
            ],
        ];

        if ( !$request->ajax() )
        {
            $response['errors'][] = 'not ajax request';

            return response()->json($response);
        }

        if ( !$request->has('term') || empty($request->term) )
        {
            $response['errors'][] = __('messages.valid_search_error');

            return response()->json($response);
        }

        $query = trim($request->term);

        if ( filter_var($query, FILTER_VALIDATE_URL) )
        {
            preg_match("#(?<=v=|v\/|vi=|vi\/|youtu.be\/)[a-zA-Z0-9_-]{11}#", $query, $match);

            if ( !isset($match[0]) )
            {
                $response['errors'][] = 'Invalid Params';

                return response()->json($response);
            }

            $query = $match[0];
            $this->limit = 1;
        }

        if ( $this->validYoutubeID($query) )
        {
            $this->limit = 1;
        }

        $cacheKey = 'search2:' . md5($query);

        $cacheData = app('cache')->get($cacheKey);

        if ( is_null($cacheData) )
        {
            $cacheData = audio()->search($query, $this->limit);

            if ( isset($cacheData['items']) && count($cacheData['items']) )
            {
                app('cache')->put($cacheKey, $cacheData, 604800); // cache in seconds, 7 day
            }
        }

        if ( isset($cacheData['items']) && count($cacheData['items']) )
        {
            $html = view('www.includes.search2-result-item', ['items' => $cacheData['items']]);

            $response['status'] = true;
            $response['message'] = 'Success';
            $response['data']['type'] = 'html';
            $response['data']['content'] = $html->render();
        }
        else
        {
            $response['message'] = 'We are sorry, not found';
        }

        return response()->json($response);
    }


    /**
     * @param $youtube_id
     * @return bool
     */
    protected function validateYoutubeID($youtube_id)
    {
        return preg_match('/^[a-zA-Z0-9_-]{11}$/', $youtube_id) > 0;
    }


}
