@extends('layouts.admin')

@section('body')

    <h1>Alphabets</h1>

    <ul>
        @foreach ($alphabets as $alphabet)

            <li>{{ $alphabet->name }}</li>

        @endforeach
    </ul>
@stop
