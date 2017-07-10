@extends('definition.edit-layout')

@section('form')

    {{-- Word --}}
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <input
                type="text"
                name="title"
                id="title"
                class="text-input center"
                placeholder="e.g. kasha"
                value="{{ $definition->titleString }}"
                autocomplete="off"
                required>
            <label for="title">
                title
            </label>
        </div>
    </div>

    {{-- Sub type --}}
    <div class="row">
        <div class="col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
            <select class="text-center en-text-input" name="subType">
                <option disabled value="">[ part of speech ]</option>
                <option>adjective</option>
                <option>adverb</option>
                <option>connective</option>
                <option>exclamation</option>
                <option>preposition</option>
                <option>pronoun</option>
                <option>noun</option>
                <option>verb</option>
                <option>intransitive verb</option>
            </select>
            <label for="subType">
                part of speech
            </label>
        </div>
    </div>

    {{-- Languages --}}
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <input
                type="text"
                id="languages"
                name="languages"
                class="text-input center"
                placeholder="e.g. Gonja"
                value="{{ implode(',', array_keys((array) $definition->languageList)) }}"
                autocomplete="off"
                required>
            <label for="languages">
                languages
            </label>
        </div>
    </div>

    {{-- Practical translation --}}
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <input
                type="text"
                id="practical"
                name="practical"
                class="en-text-input center"
                placeholder="e.g. love"
                value=""
                autocomplete="off"
                required>
            <label for="practical">
                practical <em>English</em> translation
            </label>
        </div>
    </div>

    {{-- Meaning --}}
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2">
            <input
                type="text"
                id="meaning"
                name="meaning"
                class="en-text-input center"
                placeholder="e.g. intence feeling of affection"
                value=""
                autocomplete="off"
                required>
            <label for="meaning">
                <em>English</em> meaning
            </label>
        </div>
    </div>

    {{-- Literal translation --}}
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <input
                type="text"
                id="literalTranslation"
                name="literalTranslation"
                class="en-text-input center"
                placeholder="e.g. love"
                value=""
                autocomplete="off"
                required>
            <label for="literalTranslation">
                what the definition literally translates to in <em>English</em>
            </label>
        </div>
    </div>

    {{-- Tags --}}
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <input
                type="text"
                id="tags"
                name="tags"
                class="text-input center"
                placeholder="e.g. emotion"
                value="{{ implode(',', $definition->tagList) }}"
                autocomplete="off"
                required>
            <label for="tags">
                tags
            </label>
        </div>
    </div>
@stop
