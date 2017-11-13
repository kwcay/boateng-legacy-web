@extends('layouts.half-hero')

@section('hero')

    <h1>
        Batch Edit
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
        action="{{ false ? route('definition.batch.update') : route('definition.batch.store') }}">

        {!! csrf_field() !!}
        {{ false ? method_field('PATCH') : '' }}

        @yield('form')

        <div class="row center">
            <input type="submit" name="submit" value="{{ false ? 'save' : 'add' }}">
            <input
                type="button"
                name="cancel"
                value="cancel"
                onclick="return confirm('Cancel?') ? App.redirect('/') : false;">
        </div>
    </form>

@stop
