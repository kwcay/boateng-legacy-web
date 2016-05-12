@extends('layouts.admin')

@section('body')

    <h1>Alphabets</h1>

    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.index') }}">Administration</a>
        </li>
        <li class="active">
            Alphabets

            <span style="margin: 0 10px">
                &rarr;
            </span>
            <a href="{{ route('admin.definition.index') }}">
                Definitions
            </a>
            &bull;
            <a href="{{ route('admin.language.index') }}">
                Languages
            </a>
            &bull;
            <a href="{{ route('admin.country.index') }}">
                Countries
            </a>
        </li>
    </ol>

    {{-- Pagination links --}}
    @include('admin.partials.pagination')

    <table class="table table-striped table-hover text-center">
        <thead>
            <tr>
                <td>ID</td>
                <td>Name</td>
                <td title="Alphabet code based on ISO 15924 standard for scripts">
                    Code
                </td>
                <td title="Date on which alphabet was added to database">
                    Date
                </td>
            </tr>
        </thead>

        <tbody>
            @foreach ($paginator as $alphabet)
            <tr>
                <td class="text-muted" title="# {{ $alphabet->id }}">
                    {{ $alphabet->uniqueId }}
                </td>
                <td>
                    <a href="#">
                        {{ $alphabet->name }}
                    </a>
                </td>
                <td>
                    {{ $alphabet->code }}
                </td>
                <td>
                    {{ date('M Y', strtotime($alphabet->createdAt)) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination links --}}
    @include('admin.partials.pagination')
@stop
