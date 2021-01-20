<?php

namespace App\Library;

use Illuminate\Contracts\Support\Arrayable;

class YoutubeDetail implements Arrayable
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data['items'][0] ?? $data;
    }

    public function id()
    {
        return $this['id'] ?? null;
    }

    public function publishedAt()
    {
        return $this->data['snippet']['publishedAt'] ?? null;
    }

    public function title()
    {
        return $this->data['snippet']['title'] ?? null;
    }

    public function description()
    {
        return $this->data['snippet']['description'] ?? null;
    }

    public function duration()
    {
        return static::ISO8601ToSeconds($this->data['contentDetails']['duration'] ?? 'PT0S');
    }

    public static function ISO8601ToSeconds($ISO8601)
    {
        $interval = new \DateInterval($ISO8601);

        return ($interval->d * 24 * 60 * 60) +
            ($interval->h * 60 * 60) +
            ($interval->i * 60) +
            $interval->s;
    }

    public function toArray()
    {
        return [
            'video_id'     => $this->id(),
            'published_at' => $this->publishedAt(),
            'title'        => $this->title(),
            'description'  => $this->description(),
            'duration'     => $this->duration()
        ];
    }


    /**
     * @return mixed
     */
    public function getDataSource()
    {
        return $this->data;
    }
}
