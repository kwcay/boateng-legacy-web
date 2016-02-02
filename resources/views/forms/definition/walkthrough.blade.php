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
        <input type="hidden" name="relations[language][]" value="{{ $lang->code }}">

        @yield('form')

        <!-- Form actions -->
        <br>
        <br>
        <div class="row center">
            <input type="submit" name="next" value="continue">
            <input type="submit" name="next" value="finish">
            <input type="button" name="cancel" value="return" onclick="return confirm('Cancel?') ? App.redirect('') : false;">
        </div>
    </form>

@stop
