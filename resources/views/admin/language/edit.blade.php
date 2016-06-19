@extends('layouts.admin')

@section('body')

    <h1>
        Update a Language
        <br>

        <small>
            <a href="#" onclick="return App.openDialog('del');">
                <span class="fa fa-trash-o"></span> click here to delete it
            </a>
        </small>
    </h1>

    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.index') }}">Administration</a>
        </li>
        <li>
            <a href="{{ route('admin.language.index') }}">Languages</a>
        </li>
        <li class="active">
            Update &quot;{{ $model->name }}&quot;
        </li>
    </ol>

    <form
        class="form edit"
        method="post"
        name="language"
        action="{{ route('r.language.update', $model->uniqueId) }}">

        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="return" value="{{ Request::input('return', 'summary') }}">
        {!! csrf_field() !!}

        {{-- Name --}}
        <div class="row">
            <input
                type="text"
                name="name"
                id="name"
                class="text-input"
                placeholder="e.g. Twi"
                value="{{ $model->name }}"
                autocomplete="off"
                required="required">
            <label for="name">Language name</label>
        </div>

        {{-- Transliteration --}}
        <div class="row">
            <input
                type="text"
                name="transliteration"
                id="transliteration"
                class="en-text-input"
                placeholder="e.g. Twi"
                value="{{ $model->transliteration }}"
                autocomplete="off">
            <label for="transliteration">Transliteration of language name</label>
        </div>

        {{-- Alternate names --}}
        <div class="row">
            <input
                type="text"
                name="altNames"
                id="altNames"
                class="text-input"
                placeholder="e.g. Twi"
                value="{{ $model->altNames }}"
                autocomplete="off">
            <label for="altNames">Alternate names or spellings (separated by &ldquo;,&rdquo;)</label>
        </div>

        {{-- Alphabets --}}
        <div class="row">
            <input
                id="alphabets"
                type="text"
                name="alphabets"
                class="text-input remote"
                value="{{ $model->alphabets->implode('code', ',') }}">
            <label for="alphabets">
                Alphabets this language is written in. Start typing and select an alphabet from the
                list. You can drag these around (the first will be considered the &quot;main&quot;
                alphabet).
            </label>
        </div>

        {{-- Parent language --}}
        <div class="row">
            <input
                id="parentCode"
                type="text"
                name="parentCode"
                class="text-input remote"
                value="{{ $model->parentCode }}">
            <label for="parentCode">
                Parent language
            </label>
        </div>

        {{-- Countries --}}
        <div class="row">
            <input
                id="countries"
                type="text"
                name="countries"
                class="en-text-input remote"
                value="{{ $model->countries->implode('code', ',') }}">
            <label for="countries">
                Countries where this language is chiefly spoken. Start typing and select a country
                from the list.
            </label>
        </div>

        {{-- Language code --}}
        <div class="row">
            <input
                id="code"
                type="text"
                name="code"
                value="{{ $model->code }}"
                disabled="disabled">
            <label for="code">
                <a href="http://en.wikipedia.org/wiki/ISO_639-3" target="_blank">ISO-639-3</a>
                language code
            </label>
        </div>

        <div class="row center">
            <input type="submit" name="finish" value="save">
        </div>

    </form>

    {{-- Delete confirmation --}}
    <div class="dialog del">
        <div>
            <a href="#" class="close">&#10005;</a>
            <h1>Really?</h1>
            <div class="center">
                Are you sure you want to delete the language
                <h2>&ldquo; {{ $model->name }} &rdquo;</h2>
                for ever and ever?
                <br><br>

                <form
                    class="form"
                    method="post"
                    name="delete"
                    action="{{ route('r.language.destroy', $model->uniqueId) }}">

                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="return" value="{{ Request::input('return', 'admin') }}">
                    <input type="submit" name="confirm" value="yes, delete {{ $model->name }}">
                    <input type="button" name="cancel" value="no, return" onclick="return Dialogs.close()">
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">

    {{-- Setup remote lookups --}}
    $('#alphabets').alphabetLookup({ options: {!! json_encode($alphabetOptions) !!} });
    $('#parentCode').languageLookup({ options: {!! json_encode($parentOptions) !!} });
    $('#countries').countryLookup({ options: {!! json_encode($countryOptions) !!} });

    </script>
@stop
