@extends('layouts.admin')

@section('body')

    <h1>Definitions</h1>

    <ul>
        @foreach ($definitions as $def)

            <li>{{ $def->titles[0]->title }}</li>
            
        @endforeach
    </ul>
@stop
