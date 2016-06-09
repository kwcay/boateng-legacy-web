@extends('layouts.admin')

@section('body')

    <h1>
        @yield('page-title', 'Resource listing')
    </h1>

    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.index') }}">Administration</a>
        </li>
        <li class="active">
            @yield('page-title', 'Resource listing')

            <span style="margin: 0 10px">
                &rarr;
            </span>
            <a href="{{ route('admin.definition.index') }}">
                Definitions
            </a>
            &bull;
            <a href="{{ route('admin.tag.index') }}">
                Tags
            </a>
            &bull;
            <a href="{{ route('admin.language.index') }}">
                Languages
            </a>
            &bull;
            <a href="{{ route('admin.alphabet.index') }}">
                Alphabets
            </a>
            &bull;
            <a href="{{ route('admin.country.index') }}">
                Countries
            </a>
        </li>
    </ol>

    {{-- Pagination links --}}
    @include('admin.partials.pagination')

    {{-- Query parameters --}}
    <div class="emphasis">
        @include('admin.partials.query-params')
    </div>
    <br>

    @yield('data')

    {{-- Query parameters --}}
    @if ($total >= 10)
        <div class="emphasis">
            @include('admin.partials.query-params')
        </div>
    @endif

    {{-- Pagination links --}}
    @include('admin.partials.pagination')
@stop
