@extends('layouts.admin')

@section('body')

    <h1>
        Update a User
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
            <a href="{{ route('admin.user.index') }}">Users</a>
        </li>
        <li class="active">
            Update &quot;{{ $model->name }}&quot;
        </li>
    </ol>

    <form
        class="form edit"
        method="post"
        name="user"
        action="{{ route('r.user.update', $model->uniqueId) }}">

        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="return" value="{{ Request::input('return', 'summary') }}">
        {!! csrf_field() !!}

        {{-- Name --}}
        <div class="row">
            <input
                type="text"
                name="name"
                id="name"
                class="text-input"
                placeholder="e.g. Kwasi"
                value="{{ $model->name }}"
                autocomplete="off"
                required="required">
            <label for="name">Name</label>
        </div>

        {{-- Email --}}
        <div class="row">
            <input
                type="text"
                name="email"
                id="email"
                class="en-text-input"
                placeholder="e.g. Twi"
                value="{{ $model->email }}"
                autocomplete="off">
            <label for="transliteration">Email</label>
        </div>

        <div class="row center">
            <input type="submit" name="finish" value="save">
        </div>

    </form>

    {{-- Delete confirmation --}}
    <div class="dialog del">
        <div>
            <a href="#" class="close">&#10005;</a>
            <h1>Really?</h1>
            <div class="center">
                Are you sure you want to delete the user
                <h2>&ldquo; {{ strlen($model->name) ? $model->name : $model->email }} &rdquo;</h2>
                for ever and ever?
                <br><br>

                <form
                    class="form"
                    method="post"
                    name="delete"
                    action="{{ route('r.user.destroy', $model->uniqueId) }}">

                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="return" value="{{ Request::input('return', 'admin') }}">
                    <input type="submit" name="confirm" value="yes, delete {{ strlen($model->name) ? $model->name : $model->email }}">
                    <input type="button" name="cancel" value="no, return" onclick="return Dialogs.close()">
                </form>
            </div>
        </div>
    </div>

@stop
