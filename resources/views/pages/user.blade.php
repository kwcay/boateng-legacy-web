@extends('layouts.narrow')

@section('body')

    <h1>
        {{-- Edit button --}}
        @if (Auth::check() && Auth::id() === $user->id)
            <span class="edit-res">
                <a href="{{ $user->editUri }}" class="fa fa-pencil"></a>
            </span>
        @endif

        {{ strlen($user->name) ? $user->name : $user->email }}
    </h1>

@stop
