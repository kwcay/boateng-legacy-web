@extends('layouts.half-hero')

@section('hero')

    <h1 class="hero-title">
        Learn Something New
    </h1>

@stop

@section('body')

    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-lg-6 col-lg-offset-3">
            We're building free language learning tools.
            <hr>
        </div>
    </div>

    <div class="row">
        @foreach ($languages as $lang)
            <div class="col-md-4">
                <div class="shaded-well" style="background-image:url(/img/bg/9ad291d387f72a04a57e2b4c8945f945-640x480.jpg);">
                    <a href="{{ route('language.learn', $lang['code']) }}" class="card-btn shade-50">
                        <h3>{{ $lang['name'] }}</h3>

                        {{ $lang['regions'] }}
                    </a>
                </div>
            </div>
        @endforeach
    </div>

@stop
