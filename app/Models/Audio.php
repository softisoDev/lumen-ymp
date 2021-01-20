<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audio extends Model
{
    protected $table = 'audios';

    protected $fillable = [
        'audio_id', 'upload_date', 'cover_url', 'cover_local', 'org_extension', 'download_extension', 'uploader_id', 'uploader_url', 'uploader', 'title', 'full_title', 'tags', 'description', 'file_name', 'file_on_server', 'duration', 'size', 'like_count', 'dislike_count', 'download_count', 'web_url', 'channel_url', 'channel_id'
    ];
}
