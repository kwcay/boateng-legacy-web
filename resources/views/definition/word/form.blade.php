@extends('definition.form-layout')

@section('form')

    {{-- Languages --}}
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2">
            <input
                type="text"
                id="languages"
                name="languages"
                class="text-input center"
                placeholder="e.g. Gonja"
                value="{{ implode(', ', array_keys($languages)) }}"
                autocomplete="off"
                required>
            <label for="languages">
                language or languages
            </label>
        </div>
    </div>

    {{-- Word --}}
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2">
            <input
                type="text"
                name="title"
                id="title"
                class="text-input center"
                placeholder="e.g. kasha"
                value="{{ $titleString }}"
                autocomplete="off"
                onblur="return FormHelper.checkTitle(this, document.forms.definition.languages.value)"
                required>
            <label for="title">
                title
            </label>
        </div>
    </div>

    {{-- Sub type --}}
    <div class="row">
        <div class="col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
            <select class="text-center en-text-input" id="subType" name="subType">
                @foreach ($subTypes as $subTypeValue => $subTypeOption)
                    @if ($loop->first)
                        <option disabled value="">
                            {{ $subTypeOption }}
                        </option>
                    @else
                        <option value="{{ $subTypeValue }}"{{ $subTypeValue == $subType ? ' selected' : '' }}>
                            {{ $subTypeOption }}
                        </option>
                    @endif
                @endforeach
            </select>
            <label for="subType">
                part of speech
            </label>
        </div>
    </div>

    {{-- Practical translation --}}
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2">
            <input
                type="text"
                id="practical"
                name="practical"
                class="en-text-input center"
                placeholder="e.g. love"
                value="{{ $practical }}"
                autocomplete="off"
                required>
            <label for="practical">
                <em>practical</em> English translation
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
                value="{{ $meaning }}"
                autocomplete="off">
            <label for="meaning">
                English <em>meaning</em>
            </label>
        </div>
    </div>

    {{-- Literal translation --}}
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2">
            <input
                type="text"
                id="literal"
                name="literal"
                class="en-text-input center"
                placeholder="e.g. love"
                value="{{ $literal }}"
                autocomplete="off">
            <label for="literal">
                what the definition <em>literally</em> translates to in English
            </label>
        </div>
    </div>

    {{-- Tags --}}
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2">
            <input
                type="text"
                id="tags"
                name="tags"
                class="text-input center"
                placeholder="e.g. emotion"
                value="{{ implode(', ', $tags) }}"
                autocomplete="off">
            <label for="tags">
                tags (not yet supported)
            </label>
        </div>
    </div>
@stop
