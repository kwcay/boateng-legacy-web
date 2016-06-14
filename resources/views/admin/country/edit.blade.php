@extends('layouts.admin')

@section('body')

    <h1>
        Update a Country
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
            <a href="{{ route('admin.country.index') }}">Countries</a>
        </li>
        <li class="active">
            Update &quot;{{ $model->name }}&quot;
        </li>
    </ol>

    <form
        class="form edit"
        method="post"
        name="country"
        action="{{ route('admin.country.update', $model->uniqueId) }}">

        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="return" value="{{ Request::input('return', 'admin') }}">
        {!! csrf_field() !!}

        {{-- Name --}}
        <div class="row">
            <input
                type="text"
                name="name"
                id="name"
                class="en-text-input"
                placeholder="e.g. Côte d'Ivoire"
                value="{{ $model->name }}"
                autocomplete="off"
                required="required">
            <label for="name">Country name</label>
        </div>

        {{-- Alternate names --}}
        <div class="row">
            <input
                type="text"
                name="altNames"
                id="altNames"
                class="en-text-input"
                placeholder="e.g. Cote d'Ivoire, Ivory Coast"
                value="{{ $model->altNames }}"
                autocomplete="off">
            <label for="altNames">Alternate names of spellings, separated by &quot;;&quot;</label>
        </div>

        {{-- Code --}}
        <div class="row">
            <input
                type="text"
                name="code"
                id="code"
                class="en-text-input"
                placeholder="e.g. CI"
                value="{{ $model->code }}"
                autocomplete="off"
                required="required">
            <label for="code">
                2-letter country code
            </label>
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
                Are you sure you want to delete the country
                <h2>&ldquo; {{ $model->name }} &rdquo;</h2>
                for ever and ever?
                <br><br>

                <form
                    class="form"
                    method="post"
                    name="delete"
                    action="{{ route('admin.country.destroy', $model->uniqueId) }}">

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
