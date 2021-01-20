<?php


namespace App\Console\Commands;

use App\Models\Audio;
use Exception;
use Illuminate\Console\Command;

class syncDBCommand extends Command
{
    protected $signature = 'sync:db';

    protected $description = 'Sync local files and DB';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $audios = Audio::all();

        foreach ($audios as $audio) {
            if (!file_exists(\RemoteStorage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . substr($audio->id, 0, 4) . DIRECTORY_SEPARATOR . $audio->audio_id . '.mp3')) {
                $audio->delete();
            }
        }

        $this->info('DB and Local files are synchronized successfully');
    }
}
