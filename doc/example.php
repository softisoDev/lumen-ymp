<?php

youtube('youtubeApiKey')
    ->bindRequestIp('127.0.0.1')
    ->searchVideo('paster', 5); //return array

youtube('youtubeApiKey')
    ->bindRequestIp('127.0.0.1')
    ->getVideoDetail('video-id'); //return App\Library\YoutubeDetail

youtube('youtubeApiKey')
    ->bindRequestIp('127.0.0.1')
    ->request('paster', 5); //return array

youtube('youtubeApiKey')
    ->bindRequestIp('127.0.0.1')
    ->request('videos', ['part' => 'contentDetails,snippet', 'id' => 'videoId']); //custom api request

//////////////////////////
youtube_dl('https://www.youtube.com/watch?v=YkgkThdzX-8')
    ->audioOnly('mp3')
    ->fileSave('public/imagine.mp3')
    ->run(); //download mp3

$json = youtube_dl('https://www.youtube.com/watch?v=YkgkThdzX-8')
    ->printJson()
    ->skipDownload()
    ->run(); //getvideo info json




audio('YkgkThdzX-8')->getDetail();
audio('YkgkThdzX-8')->download('public/imagine.mp3');
audio()->search('paster');
