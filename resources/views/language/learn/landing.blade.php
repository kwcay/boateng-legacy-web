@extends('layouts.half-hero')

@section('hero')

    <h1 class="hero-title">
        Learn Something New
    </h1>

@stop

@section('body')

    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-lg-6 col-lg-offset-3">
            @lang('branding.pitch')
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col text-center">
            <em>Coming Soon</em>
            <hr>
        </div>
    </div>

@stop
