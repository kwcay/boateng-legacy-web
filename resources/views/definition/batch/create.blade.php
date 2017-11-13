@extends('definition.layouts.batch')

@section('form')

    <div class="row">
        <div class="col-lg-8 col-lg-offset-2">
            <input
                type="text"
                name="title"
                id="title"
                class="text-input center"
                placeholder="e.g. kasha"
                value=""
                autocomplete="off"
                required>
            <label for="title">
                title
            </label>
        </div>
    </div>

@stop
