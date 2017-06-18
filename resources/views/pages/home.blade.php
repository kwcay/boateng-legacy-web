@extends('layouts.hero')

@section('hero')

    <h1 class="hero-title">Dora Boateng</h1>
    <h4 class="hero-subtitle">Lookup proverbs, stories, and other cultural gems.</h4>

    @include('partials.search.form')

    <div class="hero-text">
        <h4>
            Let us know what you think.
        </h4>
        <h3>
            <a href="http://goo.gl/WcthaE">Take our survey</a>
        </h3>
    </div>

@stop

@section('body')

    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-lg-6 col-lg-offset-3">
            @lang('branding.pitch')
            <hr>
        </div>
    </div>

    @include('partials.language.weekly')

@stop
