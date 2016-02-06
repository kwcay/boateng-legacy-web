@extends('forms.definition.walkthrough')

@section('page-title', 'Suggest a new word')

@section('form')

    {{-- Word --}}
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3">
            <input
                type="text"
                name="title"
                class="text-input center"
                placeholder="your word"
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
                name="relations[practical][eng]"
                class="en-text-input center"
                placeholder="your translation"
                autocomplete="off"
                required>
        </div>
    </div>

    <div class="row center">
        in English.
    </div>

@stop
