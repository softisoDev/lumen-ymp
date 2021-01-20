<?php

namespace App\Library;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Psr\Http\Message\ResponseInterface;

class Audio
{
    private static $ips;
    private static $keys;
    private static $keyIp;
    private static $blackKeys;
    private $ip;
    private $key;
    private $videoId;

    private $detail;

    public const CACHE_KEY = 'audio.blackList';

    public function __construct()
    {
        if (static::$keyIp === null) {
            $this->makeKeyIp();
        }
        $this->refreshKeyIp();
    }

    public static function id($videoId)
    {
        return (new self())->setVideoId($videoId);
    }

    public function download($file)
    {
        return youtube_dl($this->getYoutubeUrl())
            ->quiet()
            ->noWarnings()
            ->printJson()
            ->rmCacheDir()
            ->audioOnly('mp3')
            ->sourceAddress($this->ip)
            ->fileSave($file)
            ->run();
    }

    public function downloadTest($file, $printCommand = false)
    {
        $youtubeDl = youtube_dl($this->getYoutubeUrl())
            //  ->quiet()
            //  ->noWarnings()
            //  ->printJson()
            ->rmCacheDir()
            ->audioOnly('mp3')
            ->sourceAddress($this->ip)
            ->fileSave($file);

        return $printCommand
            ? $youtubeDl->commandLine()
            : $youtubeDl->run();
    }

    public function search($query, $limit = null)
    {
        $limit = $limit ?? config('app.audio.limit', 3);

        return youtube($this->key)->bindRequestIp($this->ip)
            ->whenFailed(function (ResponseInterface $response) use ($query) {
                if ($response->getStatusCode() === 403) {
                    static::addKeyToBlackList($this->key);
                    if (!empty(static::$keyIp)) {
                        $this->refreshKeyIp();
                        return $this->search($query);
                    }
                }
                return [];
            })
            ->searchVideo($query, $limit);
    }

    public function getDetail()
    {
        if ($this->detail === null) {
            $this->detail = youtube($this->key)->bindRequestIp($this->ip)
                ->whenFailed(function (ResponseInterface $response) {
                    if ($response->getStatusCode() === 403) {
                        static::addKeyToBlackList($this->key);
                        if (!empty(static::$keyIp)) {
                            $this->refreshKeyIp();
                            return $this->getDetail();
                        }
                    }
                    return new YoutubeDetail([]);
                })
                ->getVideoDetail($this->videoId);
        }
        return $this->detail;
    }

    public function setVideoId($videoId)
    {
        $this->videoId = $videoId;
        return $this;
    }

    public function getYoutubeUrl()
    {
        return 'https://www.youtube.com/watch?v=' . $this->videoId;
    }

    public function makeKeyIp($keys = null, $ips = null)
    {
        $keys = $keys ?? config('app.audio.keys');
        $ips = $ips ?? config('app.audio.ips');
        if (empty($ips)) {
            $ips = [''];
        }
        $ipValues = array_slice(
            count($keys) > count($ips)
                ? array_merge(...array_fill(0, ceil(count($keys) / count($ips)), $ips))
                : $ips
            , 0
            , count($keys)
        );

        static::$keys = $keys;
        static::$ips = $ips;
        static::$keyIp = Arr::except(array_combine($keys, $ipValues), static::getBlackListKeys());
    }

    public function refreshKeyIp()
    {
        if (!empty(static::$keyIp)) {
            $this->key = Arr::random(array_keys(static::$keyIp));
            $this->ip = static::$keyIp[$this->key];
        } else {
            $this->reportNotHaveKey();
        }
    }

    public static function addKeyToBlackList($key)
    {
        unset(static::$keyIp[$key]);
        static::$blackKeys[$key] = $key;
        Cache::put(static::CACHE_KEY, static::$blackKeys, \Carbon\Carbon::createFromTime(0, 0, 0, 'PST')->addMinute()->addDay());
    }

    public static function getBlackListKeys()
    {
        if (static::$blackKeys === null) {
            static::$blackKeys = Cache::get(static::CACHE_KEY, []);
        }
        return static::$blackKeys;
    }

    public function reportNotHaveKey()
    {

    }

}
