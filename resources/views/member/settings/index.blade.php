@extends('layouts.half-hero')

@section('title', 'Settings - '.trans('branding.title'))

@section('hero')

    <h2 class="definition-title">
        Hello, {{ $member->name }}
    </h2>
    <h4>
        Review your account settings
    </h4>

@stop

@section('body')



@stop
