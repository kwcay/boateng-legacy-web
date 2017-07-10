@extends('definition.walkthrough-layout')

@section('page-title', 'Suggest a new word')

@section('form')

    {{-- Word --}}
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <input
                type="text"
                name="titleStr"
                class="text-input center"
                placeholder="your word"
                value="{{ Request::old('titleStr') }}"
                autocomplete="off"
                required>
        </div>
    </div>

    {{-- Sub type --}}
    <div class="row">
        <div class="col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
            @include('forms.definition.subtypes', ['subType' => $definition->rawSubType])
        </div>
    </div>

    <div class="row center">
        is a word in <em>{{ $lang->name }}</em> that means
    </div>

    <!-- Translation -->
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <input
                type="text"
                name="translations[eng][practical]"
                class="en-text-input center"
                placeholder="your translation"
                value="{{ Request::old('translations[eng][practical]') }}"
                autocomplete="off"
                required>
        </div>
    </div>

    <div class="row center">
        in English.
    </div>

@stop
