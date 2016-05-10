@extends('layouts.admin')

@section('body')

    <h1>Languages</h1>

    <ul>
        @foreach ($languages as $lang)

            <li>{{ $lang->name }}</li>

        @endforeach
    </ul>
@stop
