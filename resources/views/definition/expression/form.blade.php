@extends('definition.form-layout')

@section('form')

    {{-- Expression --}}
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2">
            <input
                type="text"
                name="title"
                id="title"
                class="text-input center"
                placeholder="e.g. kasha"
                value="{{ $titleStr }}"
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
            <select class="text-center en-text-input" id="subType" name="subType">
                @foreach ($subTypes as $subTypeOption)
                    @if ($loop->first)
                        <option disabled value="">
                            {{ $subType }}
                        </option>
                    @else
                        <option value="{{ $subTypeOption }}"{{ $subTypeOption == $subType ? ' selected' : '' }}>
                            {{ $subType }}
                        </option>
                    @endif
                @endforeach
            </select>
            <label for="subType">
                part of speech
            </label>
        </div>
    </div>

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
                languages
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
                value="{{ $meaning }}"
                autocomplete="off"
                required>
            <label for="meaning">
                <em>English</em> meaning
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
                what the definition literally translates to in <em>English</em>
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
