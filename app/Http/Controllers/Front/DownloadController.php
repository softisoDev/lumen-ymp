<?php

namespace App\Http\Controllers\Front;

use App\Helpers\StringHelper;
use App\Helpers\YoutubedlHelper as Youtubedl;
use App\Library\YoutubeDetail;
use App\Models\Audio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class DownloadController
 * @package App\Http\Controllers\Front
 */
class DownloadController extends MainController
{

    /**
     * DownloadController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @param Request $request
     * @return false|RedirectResponse|string
     */
    public function getFile(Request $request)
    {
        $audio = Audio::where('audio_id', $request->videoId)->first();

        if ( !is_null($audio) )
        {
            $url = $this->generateDownloadUrl($audio->id, $audio->audio_id, $audio->title);

            return \redirect()->to($url);
        }

        $detail = audio($request->videoId)->getDetail()->getDataSource();

        if ( !is_array($detail) && !isset($detail['id']) )
        {
            return redirect(url('error/oops/?code=1'));
        }

        if ( YoutubeDetail::ISO8601ToSeconds($detail['contentDetails']['duration']) > 1200 )
        {
            return redirect(url('error/long-video'));
        }

        //save data to db
        $audio = $this->save2DB($detail);

        if (!$audio)
        {
            return redirect(url('error/oops/?code=2'));
        }

        $dl = Youtubedl::instance();

        //set download path

        // $fullPath = \RemoteStorage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . substr($audio->id, 0, 4) . '/';

        $fullPath = '/var/www/youtube/data/www/s1/public/storage/' . substr($audio->id, 0, 4) . '/';

        if ( !is_dir($fullPath) )
        {
            mkdir($fullPath);
        }

        $file = $fullPath . $request->videoId . '.mp3';

        $download = \audio($request->videoId)->download($file);

        if (!file_exists($file))
        {
            $audio->delete();

            return redirect(url('error/oops/?code=3'));
        }

        self::saveFileSize2DB($audio->id, $fullPath);

        $url = self::generateDownloadUrl($audio->id, $detail['id'], $detail['snippet']['title']);

        sleep(1);

        return \redirect()->to($url);
    }


    /**
     * @param $path
     * @param $audioId
     * @param $downloadName
     * @return mixed
     */
    public function generateDownloadUrl($path, $audioId, $downloadName)
    {
        return \RemoteStorage::disk('local')->url(substr($path, 0, 4) . '/' . $audioId . '.mp3?n=' . (new StringHelper())->seo_friendly($downloadName));
    }


    /**
     * @param $id
     * @param $dir
     */
    public function saveFileSize2DB($id, $dir)
    {
        $audio = Audio::where('id', $id)->first();

        try {
            $audio->update([
                'size' => filesize($dir . DIRECTORY_SEPARATOR . $audio->audio_id . '.mp3'),
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage() . $e->getLine() . $e->getFile());
        }

    }

    /**
     * @param $detail
     * @return bool
     */
    public function save2DB($detail)
    {
        //save data to db
        return Audio::firstOrCreate([
            'audio_id'    => $detail['id'],
            'upload_date' => date('Y-m-d', strtotime($detail['snippet']['publishedAt'])),
            'title'       => clear_string($detail['snippet']['title']),
            'description' => $detail['snippet']['description'],
            'duration'    => YoutubeDetail::ISO8601ToSeconds($detail['contentDetails']['duration']),
        ]);
    }


}
