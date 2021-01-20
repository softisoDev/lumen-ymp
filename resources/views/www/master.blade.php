<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>{{ __('messages.title') }}</title>
        <link rel="icon" href="{{\Illuminate\Support\Facades\URL::asset('img/main.ico')}}" type="image/x-icon">
        <link rel="stylesheet" href="{{\Illuminate\Support\Facades\URL::asset('/style/fontello.css')}}">
        <link rel="stylesheet" href="{{\Illuminate\Support\Facades\URL::asset('/style/animation.css')}}">
        <link rel="stylesheet" href="{{\Illuminate\Support\Facades\URL::asset('/style/style.css')}}">
        <script type="text/javascript">
            var app = {
                host: {!! json_encode(url('/')) !!}+"",
                locale: "{{ $locale }}",
                youtubeUrl: "https://www.youtube.com/watch?v=",
                translate: {
                    "title": "<?= __('messages.v_title') ?>",
                    "description": "<?= __('messages.v_description') ?>",
                    "publish_date": "<?= __('messages.v_publish_date') ?>",
                    "duration": "<?= __('messages.v_duration') ?>",
                    'general_error_msg': "<?= __('messages.general_error_msg') ?>",
                    'valid_search_error': "<?= __('messages.valid_search_error') ?>",
                    'download_button': "<?= __('messages.download_button') ?>",
                    'download_wait_button': "<?= __('messages.download_wait_button') ?>",
                }
            }
        </script>
    </head>
    <body>
        @yield('content')
        <script type="text/javascript" src="{{ \Illuminate\Support\Facades\URL::asset('/script/main.js') }}"></script>
    </body>
</html>
