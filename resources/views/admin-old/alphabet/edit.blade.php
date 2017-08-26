@extends('layouts.admin')

@section('body')

    <h1>
        Update an Alphabet
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
            <a href="{{ route('admin.alphabet.index') }}">Alphabets</a>
        </li>
        <li class="active">
            Update &quot;{{ $model->name }}&quot;
        </li>
    </ol>

    <form
        class="form edit"
        method="post"
        name="alphabet"
        action="{{ route('r.alphabet.update', $model->uniqueId) }}">

        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="return" value="{{ Request::input('return', 'admin') }}">
        {!! csrf_field() !!}

        {{-- Name --}}
        <div class="row">
            <input
                type="text"
                name="name"
                id="name"
                class="text-input"
                placeholder="e.g. Twi Alphabet"
                value="{{ $model->name }}"
                autocomplete="off"
                required="required">
            <label for="name">Alphabet name</label>
        </div>

        {{-- Transliteration --}}
        <div class="row">
            <input
                type="text"
                name="transliteration"
                id="transliteration"
                class="en-text-input"
                placeholder="e.g. Twi Alphabet"
                value="{{ $model->transliteration }}"
                autocomplete="off">
            <label for="transliteration">Transliteration of alphabet name</label>
        </div>

        {{-- Code --}}
        <div class="row">
            <input
                type="text"
                name="code"
                id="code"
                class="en-text-input"
                placeholder="e.g. twi-Latn"
                value="{{ $model->code }}"
                autocomplete="off"
                required="required">
            <label for="code">
                Alphabet code, based on
                <a href="http://en.wikipedia.org/wiki/ISO_15924" target="_blank">ISO 15924</a>
                standard. This will usually begin with the
                <a href="http://en.wikipedia.org/wiki/ISO_639-3" target="_blank">ISO-639-3</a>
                3-letter code of the main language using this alphabet, followed by a &quot;-&quot;
                (dash), and finally end with the 4-letter code of the alphabet script.
            </label>
        </div>

        {{-- Script code --}}
        <div class="row">
            <input
                type="text"
                name="scriptCode"
                id="scriptCode"
                class="en-text-input"
                placeholder="e.g. Latn"
                value="{{ $model->scriptCode }}"
                autocomplete="off">
            <label for="code">
                Script code (see
                <a href="http://en.wikipedia.org/wiki/ISO_15924" target="_blank">ISO 15924</a>
                standard).
            </label>
        </div>

        {{-- Alphabet letters --}}
        <div class="row">
            <textarea
                name="letters"
                id="name"
                class="text-input">{{ $model->letters }}</textarea>
            <label for="letters">Letters of this alphabet</label>
        </div>

        <div class="row center">
            <input type="submit" name="finish" value="save">
        </div>

    </form>

    {{-- Delete confirmation --}}
    <div class="dialog del">
        <div>
            <a href="#" class="close">&#10005;</a>
            <h1>Are you sure?</h1>
            <div class="center">
                Are you sure you want to delete the alphabet
                <h2>&ldquo; {{ $model->name }} &rdquo;</h2>
                for ever and ever?
                <br><br>

                <form
                    class="form"
                    method="post"
                    name="delete"
                    action="{{ route('r.alphabet.destroy', $model->uniqueId) }}">

                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="return" value="{{ Request::input('return', 'admin') }}">
                    <input type="submit" name="confirm" value="yes, delete {{ $model->name }}">
                    <input type="button" name="cancel" value="no, return" onclick="return Dialogs.close()">
                </form>
            </div>
        </div>
    </div>
@stop
