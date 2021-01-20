<?php


namespace App\Library;

use Symfony\Component\Process\Process;

class YoutubeDL
{
    private $url;
    private $options = [];
    private $process;

    public static function url($url)
    {
        return (new self())->setUrl($url);
    }

    public function noWarnings()
    {
        return $this->option('--no-warnings');
    }

    public function quiet()
    {
        return $this->option('--quiet');
    }

    /**
     * example socks5://127.0.0.1:1080/
     */
    public function proxy($proxyUrl)
    {
        return $this->option('--proxy', $proxyUrl);
    }

    public function sourceAddress($ip)
    {
        if (!empty($ip)) {
            return $this->option('--source-address', $ip);
        }
        return $this;
    }

    public function userAgent($userAgent)
    {
        return $this->option('--user-agent', $userAgent);
    }

    public function referer($referer)
    {
        return $this->option('--referer', $referer);
    }

    public function extractAudio()
    {
        return $this->option('--extract-audio');
    }

    public function audioOnly($format = 'mp3')
    {
        return $this->extractAudio()->audioFormat($format);
    }

    public function audioFormat($format)
    {
        return $this->option('--audio-format', $format);
    }

    public function audioQuality($quality)
    {
        return $this->option('--audio-quality', $quality);
    }

    public function printJson()
    {
        return $this->option('--print-json');
    }

    public function skipDownload()
    {
        return $this->option('--skip-download');
    }

    public function rmCacheDir()
    {
        return $this->option('--rm-cache-dir');
    }

    /**
     * direct file name or format '%(title)s.%(ext)s'
     * @link https://github.com/ytdl-org/youtube-dl#output-template
     */
    public function output($outputFormat)
    {
        return $this->option('--output', $outputFormat);
    }

    public function fileSave($path)
    {
        return $this->output($path);
    }

    public function run()
    {
        $process = $this->getProcess();
        $process->run();
        return trim($process->getOutput());
    }

    public function runBackground($callback = null)
    {
        $process = $this->getProcess();
        $process->start($callback);
        return $process;
    }

    public function commandLine()
    {
        return $this->getProcess()->getCommandLine();
    }

    private function makeProccess()
    {
        return $this->process = new Process(array_merge([$this->getYoutubeDlSource()], $this->getOptionsArray(), [$this->url]));
    }

    public function version()
    {
        $this->options['--version'] = null;
        return $this->run();
    }

    private function getYoutubeDlSource()
    {
        return env('YOUTUBE_DL_SOURCE', 'youtube-dl');
    }

    public function getOptionsArray()
    {
        $options = [];
        foreach ($this->options as $option => $value) {
            $options[] = $option;
            if (!empty($value)) {
                $options[] = $value;
            }
        }
        return $options;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param $option
     * @param $value
     */
    public function option($option, $value = null)
    {
        $this->options[$option] = $value;
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return Process
     */
    public function getProcess(): Process
    {
        return $this->process ?? $this->makeProccess();
    }

}
