@extends('layouts.half-hero')

@section('hero')

    <h1>
        @yield('page-title', 'Update')
    </h1>

    <h4>
        And help improve @lang('branding.title') for everyone.
    </h4>

@stop

@section('body')

    <form
        class="edit form"
        method="post"
        name="definition"
        action="">

        {!! csrf_field() !!}
        <input type="hidden" name="type" value="{{ $type }}">
        <input type="hidden" name="languages[]" value="">

        @yield('form')

        {{-- Form actions --}}
        <br>
        <br>
        <div class="row center">
            @if (Auth::check())
            <input type="submit" name="return" value="continue">
            <input type="submit" name="return" value="finish">
            @else
            <input type="submit" name="return" value="save">
            @endif
            <input
                type="button"
                name="cancel"
                value="cancel"
                onclick="return confirm('Cancel?') ? App.redirect('/') : false;">
        </div>
    </form>

@stop
