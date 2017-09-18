@extends('layouts.half-hero')

@section('hero')

    <h1 class="hero-title">
        Dora Boateng
    </h1>

    @unless($query)
        <h4 class="hero-subtitle">
            Lookup proverbs, stories, and other cultural gems.
        </h4>
    @endunless

    @include('partials.search.form')

@stop

@section('body')

    @include('partials.search.results')

    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-lg-6 col-lg-offset-3">
            @lang('branding.pitch')
            <br>
            <br>

            <a href="http://eepurl.com/cKEMKP" target="_blank">Get notified</a> as we add new
            content and features. If you're interested in sharing your feedback or ideas,
            don't hesitate to <a href="http://goo.gl/WcthaE">take our survey</a> or
            <a href="mailto:frank@doraboateng.com">shoot us an email</a>.
            <hr>
        </div>
    </div>

    @if ($language)
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="shaded-well" style="background-image:url(/img/bg/9ad291d387f72a04a57e2b4c8945f945-640x480.jpg);">
                <a href="{{ route('language', $language->code) }}" class="card-btn shade-50">
                    Language of the week:

                    <h3>{{ $language->name }}</h3>
                </a>
            </div>
        </div>
    </div>
    @endif

@stop
