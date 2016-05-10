@extends('layouts.admin')

@section('body')

    <h1>Countries</h1>

    <ul>
        @foreach ($countries as $country)

            <li>{{ $country->name }}</li>

        @endforeach
    </ul>
@stop
