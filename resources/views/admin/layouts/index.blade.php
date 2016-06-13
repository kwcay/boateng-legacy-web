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
