<div class="results">
    @foreach($items as $k => $v)
        <div class="item">
            <div class="image">
                <img src="https://i.ytimg.com/vi/{{ $v['id']['videoId'] }}/default.jpg" class="artist-img">
            </div>
            <div class="info">
                <h3>{{ clear_string($v['snippet']['title']) }}</h3>
            </div>
            <div class="actions">
                <a href="{{ url('get', ['id' => $v['id']['videoId']]) }}" class="action action_mp3" data-action="mp3" data-video-id="{{ $v['id']['videoId'] }}">
                    <span class="icon show"><i class="icon-download-2"></i></span>
                    <span class="loader"><i class="icon-spin3 animate-spin"></i></span>
                </a>
            </div>
        </div>
    @endforeach
</div>
