@extends('layouts.half-hero')

@section('hero')

    <h1>
        @if ($isNew)
            Add a Language
        @else
            <small>edit</small> {{ $name }}
        @endif
    </h1>

    <h4>
        And help improve @lang('branding.title') for everyone.
    </h4>

@stop

@section('body')

    <form
        class="edit form"
        method="post"
        name="language"
        action="{{ $isNew ? route('language.store') : route('language.update', $code) }}">

        {!! csrf_field() !!}
        {{ $isNew ? '' : method_field('PATCH') }}

        <div class="row center">
            Add a language named
        </div>

        {{-- Name --}}
        <div class="row">
            <div class="col-lg-10 col-lg-offset-1">
                <input
                    type="text"
                    name="name"
                    class="text-input center"
                    placeholder="e.g. Fante"
                    value="{{ $name  }}"
                    autocomplete="off"
                    required>
                <label for="name">
                    language name(s), separated by commas
                </label>
            </div>
        </div>
        <br>

        <div class="row center">
            which has the 3-letter
            <a href="http://en.wikipedia.org/wiki/ISO_639-3" target="_blank">
                ISO-639-3
            </a>
            code
        </div>

        {{-- ISO-639-3 --}}
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3 col-lg-4 col-lg-offset-4">
                <input
                    type="text"
                    name="code"
                    class="en-text-input center"
                    placeholder="e.g. fat"
                    value="{{ $code }}"
                    autocomplete="off"
                    required>
                <label for="code">
                    language code
                </label>
            </div>
        </div>
        <br>

        <div class="row center">
            and the parent language
        </div>

        {{-- Parent language --}}
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3 col-lg-4 col-lg-offset-4">
                <input
                    type="text"
                    name="parent_code"
                    class="en-text-input center"
                    placeholder="e.g. Twi"
                    value="{{ $parentCode }}"
                    autocomplete="off">
                <label for="code">
                    parent language
                </label>
            </div>
        </div>
        <br>

        <div class="row center">
            to @lang('branding.title')
        </div>
        <br>

        <div class="row center">
            <input type="submit" name="submit" value="{{ $isNew ? 'add' : 'save' }}">
            <input
                type="button"
                name="cancel"
                value="cancel"
                onclick="return confirm('Cancel?') ? App.redirect('/') : false;">
        </div>
    </form>

@stop
