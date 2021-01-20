@extends('www.master')


@section('content')
    <div class="wrapper hide" id="message"></div>

    <div class="wrapper search">
        <form method="post" action="/search/" data-form="search">
            <input autocomplete="off" name="q" type="text" placeholder="{{ __('messages.search_placeholder') }}">
            <button>
                <span class="text">{{ __('messages.search_button') }}</span>
                <span class="icon"><i class="icon-search"></i></span>
                <span class="loader"><i class="icon-spin3 animate-spin"></i></span>
            </button>
        </form>
    </div>
@endsection
