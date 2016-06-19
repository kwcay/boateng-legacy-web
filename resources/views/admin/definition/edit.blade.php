@extends('layouts.admin')

@section('body')

    <h1>
        Update a Definition
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
            <a href="{{ route('admin.definition.index') }}">Definitions</a>
        </li>
        <li class="active">
            Update &quot;{{ $model->titles[0]->title }}&quot;
        </li>
    </ol>

    <form
        class="form edit"
        method="post"
        name="definition"
        action="{{ route('r.definition.update', ['id' => $model->uniqueId]) }}">

        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="return" value="{{ Request::input('return', 'summary') }}">
        {!! csrf_field() !!}

        {{-- Titles --}}
        <div class="row">
            <input
                type="text"
                name="titleStr"
                id="titleStr"
                class="text-input"
                placeholder="e.g. ɔdɔ, dɔ"
                value="{{ $model->titles->implode('title', ', ') }}"
                autocomplete="off"
                required>
            <label for="titleStr">Spellings (separated by &ldquo;,&rdquo;)</label>
        </div>


        {{-- Type --}}
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4 col-lg-2 col-lg-offset-5">
                <select class="text-center en-text-input" name="type" id="type">
                    @foreach ($model->types as $typeId => $typeName)
                        <option
                            value="{{ $typeId }}"
                            {{ $model->type == $typeName ? ' selected' : '' }}>
                            {{ $typeName }}
                        </option>
                    @endforeach
                </select>
                <label for="type">Type</label>
            </div>
        </div>


        {{-- Sub type --}}
        <div class="row">
            <div class="col-sm-8 col-sm-offset-1 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
                <select class="text-center en-text-input" name="subType" id="subType">
                    <optgroup label="[Parts of Speech]">
                        <option
                            value="adj"
                            {{ $model->rawSubType == 'adj' ? ' selected' : '' }}>
                            adjective
                        </option>
                        <option
                            value="adv"
                            {{ $model->rawSubType == 'adv' ? ' selected' : '' }}>
                            adverb
                        </option>
                        <option
                            value="conn"
                            {{ $model->rawSubType == 'conn' ? ' selected' : '' }}>
                            connective
                        </option>
                        <option
                            value="ex"
                            {{ $model->rawSubType == 'ex' ? ' selected' : '' }}>
                            exclamation
                        </option>
                        <option
                            value="pre"
                            {{ $model->rawSubType == 'pre' ? ' selected' : '' }}>
                            preposition
                        </option>
                        <option
                            value="pro"
                            {{ $model->rawSubType == 'pro' ? ' selected' : '' }}>
                            pronoun
                        </option>
                        <option
                            value="n"
                            {{ $model->rawSubType == 'n' ? ' selected' : '' }}>
                            noun
                        </option>
                        <option
                            value="v"
                            {{ $model->rawSubType == 'v' ? ' selected' : '' }}>
                            verb
                        </option>
                        <option
                            value="intv"
                            {{ $model->rawSubType == 'intv' ? ' selected' : '' }}>
                            intransitive verb
                        </option>
                    </optgroup>
                    <optgroup label="[Morphemes]">
                        <option
                            value="prefix"
                            {{ $model->rawSubType == 'prefix' ? ' selected' : '' }}>
                            prefix
                        </option>
                        <option
                            value="suffix"
                            {{ $model->rawSubType == 'suffix' ? ' selected' : '' }}>
                            suffix
                        </option>
                    </optgroup>
                    <optgroup label="[Expressions]">
                        <option
                            value="expression"
                            {{ $model->rawSubType == 'expression' ? ' selected' : '' }}>
                            common expression
                        </option>
                        <option
                            value="phrase"
                            {{ $model->rawSubType == 'phrase' ? ' selected' : '' }}>
                            simple phrase
                        </option>
                        <option
                            value="proverb"
                            {{ $model->rawSubType == 'proverb' ? ' selected' : '' }}>
                            proverb or saying
                        </option>
                    </optgroup>
                </select>
                <label for="subType">Sub type</label>
            </div>
        </div>

        {{-- Translation --}}
        <div class="row">
            <input
                type="text"
                id="translations[eng][practical]"
                name="translations[eng][practical]"
                class="en-text-input"
                placeholder="e.g. love"
                value="{{ $model->getPracticalTranslation('eng') }}"
                autocomplete="off"
                required>
            <label for="translations[eng][practical]">English translation</label>
        </div>

        {{-- Meaning --}}
        <div class="row">
            <input
                type="text"
                id="translations[eng][meaning]"
                name="translations[eng][meaning]"
                class="en-text-input"
                placeholder="e.g. an intense feeling of deep affection."
                value="{{ $model->getMeaning('eng') }}"
                autocomplete="off">
            <label for="relations[meaning][eng]">English meaning or synonyms</label>
        </div>

        {{-- Literal translation --}}
        <div class="row">
            <input
                type="text"
                id="translations[eng][literal]"
                name="translations[eng][literal]"
                class="en-text-input"
                placeholder=""
                value="{{ $model->getLiteralTranslation('eng') }}"
                autocomplete="off">
            <label for="translations[eng][literal]">Literal translation</label>
        </div>

        {{-- Languages --}}
        <div class="row">
            <input
                id="languages"
                type="text"
                name="languages"
                class="text-input remote"
                value="{{ $model->languages->implode('code', ',') }}">
            <label for="languages">
                Languages that use this word. Start typing and select a language from the list.
                You can drag these around (the first will be considered the &quot;main&quot;
                language).
            </label>
        </div>

        {{-- Related definitions --}}
        <div class="row">
            <input
                id="relatedDefinitions"
                type="text"
                name="relatedDefinitions"
                class="text-input remote"
                value="{{ $model->relatedDefinitionList->implode('uniqueId', ',') }}">
            <label for="relatedDefinitions">
                Related definitions
            </label>
        </div>

        {{-- Tags --}}
        <div class="row">
            <input
                id="tags"
                type="text"
                name="tags"
                class="text-input remote"
                value="{{ $model->tags->implode('title', ',') }}">
            <label for="tags">
                Tags
            </label>
        </div>

        <div class="row center">
            <input type="submit" name="finish" value="save">
        </div>

    </form>

    <!-- Delete confirmation -->
    <div class="dialog del">
        <div>
            <a href="#" class="close">&#10005;</a>
            <h1>Really?</h1>
            <div class="center">
                Are you sure you want to delete the definition for
                <h2>&ldquo; {{ $model->titles[0]->title }} &rdquo;</h2>
                for ever and ever?
                <br><br>

                <form
                    class="form"
                    method="post"
                    name="delete"
                    action="{{ route('r.definition.destroy', ['id' => $model->uniqueId]) }}">

                    <input
                        type="hidden"
                        name="_method"
                        value="DELETE">
                    <input
                        type="submit"
                        name="confirm"
                        value="yes, delete {{ $model->titles[0]->title }}">
                    <input
                        type="button"
                        name="cancel"
                        value="no, return"
                        onclick="return Dialogs.close()">

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

    $('#tags').tagSearch();

    $('#relatedDefinitions').definitionLookup({
        lang: '{{ $model->mainLanguageCode }}',
        options: {!! json_encode($relatedDefinitionOptions) !!}
    });

    </script>
@stop
