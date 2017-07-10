@extends('layouts.half-hero')

@section('hero')

    <h1>
        @yield('page-title', 'Update '. $definition->mainTitle)
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
        <input type="hidden" name="type" value="{{ $definition->resourceType }}">
        <input type="hidden" name="languages[]" value="{{ implode(',', array_keys((array) $definition->languageList)) }}">

        @yield('form')

        {{-- Form actions --}}
        <br>
        <br>
        <div class="row center">
            <input type="submit" name="return" value="save">
            <input
                type="button"
                name="cancel"
                value="cancel"
                onclick="return confirm('Cancel?') ? App.redirect('/') : false;">
        </div>
    </form>

@stop
