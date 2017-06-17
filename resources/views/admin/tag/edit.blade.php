@extends('layouts.admin')

@section('body')

    <h1>
        Update a Tag
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
            <a href="{{ route('admin.tag.index') }}">Tags</a>
        </li>
        <li class="active">
            Update &quot;{{ $model->title }}&quot;
        </li>
    </ol>

    <form
        class="form edit"
        method="post"
        name="tag"
        action="{{ route('r.tag.update', $model->uniqueId) }}">

        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="return" value="{{ Request::input('return', 'summary') }}">
        {!! csrf_field() !!}

        {{-- Title --}}
        <div class="row">
            <input
                type="text"
                name="title"
                id="title"
                class="en-text-input"
                placeholder="e.g. animal"
                value="{{ $model->title }}"
                autocomplete="off"
                required="required">
            <label for="title">Tag title</label>
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
                Are you sure you want to delete the tag
                <h2>&ldquo; {{ $model->title }} &rdquo;</h2>
                for ever and ever?
                <br><br>

                <form
                    class="form"
                    method="post"
                    name="delete"
                    action="{{ route('r.tag.destroy', $model->uniqueId) }}">

                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="return" value="{{ Request::input('return', 'admin') }}">
                    <input type="submit" name="confirm" value="yes, delete {{ $model->title }}">
                    <input type="button" name="cancel" value="no, return" onclick="return Dialogs.close()">
                </form>
            </div>
        </div>
    </div>
@stop
