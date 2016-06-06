@extends('layouts.narrow')

@section('body')

    <h1>
        @yield('page-title', 'Suggest a new definition')
        <br>

        <small>
            <a href="{{ route('language.create')  }}">
                &rarr; or click here to suggest a language
            </a>
        </small>
    </h1>
    <br>
    <br>

    <form
        class="edit form"
        method="post"
        name="definition"
        action="{{ route('definition.store') }}">

        {!! csrf_field() !!}
        <input type="hidden" name="type" value="{{ $type }}">
        <input type="hidden" name="languages[]" value="{{ $lang->code }}">

        @yield('form')

        <!-- Form actions -->
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
                onclick="return confirm('Cancel?') ? App.redirect('{{ $lang->code }}') : false;">
        </div>
    </form>

@stop
