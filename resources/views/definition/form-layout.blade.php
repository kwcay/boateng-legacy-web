@extends('layouts.half-hero')

@section('hero')

    <h1>
        <small>edit</small> {{ $definition->mainTitle }}
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
        action="{{ $id ? route('definition.update', $id) : route('definition.create') }}">

        {!! csrf_field() !!}
        {{ $id ? method_field('PATCH') : '' }}
        <input type="hidden" name="type" value="definition">

        @yield('form')

        <div class="row center">
            <input type="submit" name="submit" value="save">
            <input
                type="button"
                name="cancel"
                value="cancel"
                onclick="return confirm('Cancel?') ? App.redirect('/') : false;">
        </div>
    </form>

@stop
