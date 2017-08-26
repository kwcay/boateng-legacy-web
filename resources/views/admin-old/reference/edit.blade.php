@extends('layouts.admin')

@section('body')

    <h1>
        Update a Reference
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
            <a href="{{ route('admin.reference.index') }}">References</a>
        </li>
        <li class="active">
            Update &quot;{{ $model->typeName }}&quot; reference
        </li>
    </ol>

    <form
        class="form edit"
        method="post"
        name="reference"
        action="{{ route('r.reference.update', $model->uniqueId) }}">

        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="return" value="{{ Request::input('return', 'admin') }}">
        <input type="hidden" name="type" value="{{ $model->type }}">
        {!! csrf_field() !!}

        @include('admin.reference.edit-'. $model->type)

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
                Are you sure you want to delete the reference
                <h2>&ldquo; {{ $model->name }} &rdquo;</h2>
                for ever and ever?
                <br><br>

                <form
                    class="form"
                    method="post"
                    name="delete"
                    action="{{ route('r.reference.destroy', $model->uniqueId) }}">

                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="return" value="{{ Request::input('return', 'admin') }}">
                    <input type="submit" name="confirm" value="yes">
                    <input type="button" name="cancel" value="no, return" onclick="return Dialogs.close()">
                </form>
            </div>
        </div>
    </div>
@stop
