@extends('layouts.narrow')

@section('body')

    <h1>
        Edit an <i>existing</i>
        <br>

        <em>definition</em>
        <br>

        <small>
            <a href="#" onclick="return App.openDialog('del');">
                <span class="fa fa-trash-o"></span> click here to delete it
            </a>
        </small>
    </h1>

    <form
        class="form edit"
        method="post"
        name="definition"
        action="{{ route('definition.update', ['id' => $definition->uniqueId]) }}">

        <input type="hidden" name="_method" value="PUT">
        {!! csrf_field() !!}

        {{-- Title --}}
        <div class="row">
            <input
                type="text"
                name="title"
                id="title"
                class="text-input"
                placeholder="e.g. ɔdɔ"
                value="{{ $definition->title }}"
                autocomplete="off"
                required>
            <label for="title">Title</label>
        </div>

        {{-- Alternate spellings --}}
        <div class="row">
            <input
                type="text"
                id="altTitles"
                name="altTitles"
                class="text-input"
                placeholder="e.g. do, dɔ, odo "
                value="{{ $definition->altTitles }}"
                autocomplete="off">
            <label for="altTitles">Alternate titles or spellings (seperated by ",")</label>
        </div>

        {{-- Type --}}
        <div class="row">
            @include('forms.definition.subtypes', ['subType' => $definition->rawSubType])
            <label for="subType">Sub type</label>
        </div>

        {{-- Translation --}}
        <div class="row">
            <input
                type="text"
                id="relations[practical][eng]"
                name="relations[practical][eng]"
                class="en-text-input"
                placeholder="e.g. love"
                value="{{ $definition->getPracticalTranslation('eng') }}"
                autocomplete="off"
                required>
            <label for="relations[practical][eng]">English translation</label>
        </div>

        {{-- Meaning --}}
        <div class="row">
            <input
                type="text"
                id="relations[meaning][eng]"
                name="relations[meaning][eng]"
                class="en-text-input"
                placeholder="e.g. an intense feeling of deep affection."
                value="{{ $definition->getMeaning('eng') }}"
                autocomplete="off">
            <label for="relations[meaning][eng]">English meaning</label>
        </div>

        {{-- Literal translation --}}
        <div class="row">
            <input
                type="text"
                id="relations[literal][eng]"
                name="relations[literal][eng]"
                class="en-text-input"
                placeholder=""
                value="{{ $definition->getLiteralTranslation('eng') }}"
                autocomplete="off">
            <label for="relations[literal][eng]">Literal translation</label>
        </div>

        {{-- Language --}}
        <div class="row">
            <input
                id="languages"
                type="text"
                name="relations[language]"
                class="text-input remote"
                value="{{ $languageValue }}">
            <label for="languages">
                Languages that use this word. Start typing and select a language from the list.
                You can drag these around (the first will be considred the "main" language).
            </label>
        </div>

        <!-- Form actions -->
        <div class="row center">
            <input type="submit" name="finish" value="done !">
            <input type="button" name="cancel" value="cancel" onclick="return confirm('Cancel editing?') ? App.redirect('') : false;">
        </div>

    </form>

    <!-- Delete confirmation -->
    <div class="dialog del">
        <div>
            <a href="#" class="close">&#10005;</a>
            <h1>Really?</h1>
            <div class="center">
                Are you sure you want to delete the definition for
                <h2>&ldquo; {{ $definition->title }} &rdquo;</h2>
                for ever and ever?
                <br><br>

                <form
                    class="form"
                    method="post"
                    name="delete"
                    action="{{ route('definition.destroy', ['id' => $definition->uniqueId]) }}">

                    <input type="hidden" name="_method" value="DELETE">
                    <input type="submit" name="confirm" value="yes, delete {{ $definition->title }}">
                    <input type="button" name="cancel" value="no, return" onclick="return Dialogs.close()">
		            {!! Form::token() !!}
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">

    // Setup language search for "lanuguage" field
    Forms.setupLangSearch(
        '#languages',
        {!! json_encode($languageOptions) !!},
        20,
        ['remove_button', 'drag_drop']
    );

    //$(document).ready(function() { $('input[name="word"]').focus(); });

    </script>

@stop
