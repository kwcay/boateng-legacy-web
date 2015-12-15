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
        action="{{ route('definition.update', ['id' => $def->uniqueId]) }}">

        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="more" value="0">
        {!! csrf_field() !!}

        {{-- Title --}}
        <div class="row">
            <input type="text" name="title" class="text-input" placeholder="e.g. ɔdɔ" value="{{ $def->title }}">
            <label for="title">Title</label>
        </div>

        {{-- Alternate spellings --}}
        <div class="row">
            <input
                type="text"
                id="alt_titles"
                name="alt_titles"
                class="text-input"
                placeholder="e.g. do, dɔ, odo "
                value="{{ $def->altTitles }}" />
            <label for="alt_titles">Alternate titles or spellings (seperated by ",")</label>
        </div>

        {{-- Type --}}
        <div class="row">
            {!! Form::select('sub_type', $def->getSubTypes(), $def->subType, array('class' => 'en-text-input')) !!}
            <label for="type">Sub type</label>
        </div>

        {{-- Translation --}}
        <div class="row">
            <input
                type="text"
                id="relations[practical][eng]"
                name="relations[practical][eng]"
                class="en-text-input"
                placeholder="e.g. love"
                value="{{ $def->getPracticalTranslation('eng') }}" />
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
                value="{{ $def->getMeaning('eng') }}" />
            <label for="relations[meaning][eng]">English meaning</label>
        </div>

        {{-- Language --}}
        <div class="row">
            <input id="languages" type="text" name="relations[language]" class="text-input remote" value="">
            <label for="languages">
                Languages that use this word. Start typing and select a language from the list.
                You can drag these around (the first will be considred the "main" language).
            </label>
        </div>

        <!-- Form actions -->
        <div class="row center">
            <input type="submit" name="finish" value="done !" disabled>
            <input type="submit" name="new" value="save + add" onclick="return saveAndNew();" disabled>
            <input type="button" name="cancel" value="return" onclick="return confirm('Cancel editing?') ? App.redirect('') : false;">
        </div>

    </form>

    <!-- Delete confirmation -->
    <div class="dialog del">
        <div>
            <a href="#" class="close">&#10005;</a>
            <h1>Really?</h1>
            <div class="center">
                Are you sure you want to delete the definition for
                <h2>&ldquo; {{ $def->title }} &rdquo;</h2>
                for ever and ever?
                <br><br>

                <form
                    class="form"
                    method="post"
                    name="delete"
                    action="{{ route('definition.destroy', ['id' => $def->uniqueId]) }}">

                    <input type="hidden" name="_method" value="DELETE">
                    <input type="submit" name="confirm" value="yes, delete {{ $def->title }}">
                    <input type="button" name="cancel" value="no, return" onclick="return App.closeDialogs()">
		            {!! Form::token() !!}
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">

    // Word types
    //$('select[name="type"]').selectize();

    // Setup language search for "lanuguage" field
    Forms.setupLangSearch('#languages', {!! json_encode($options) !!}, 20, ['remove_button', 'drag_drop']);

    var saveAndNew = function() {
        document.definition.more.value = 1;
        document.definition.submit();
    };

    //$(document).ready(function() { $('input[name="word"]').focus(); });

    </script>

@stop
