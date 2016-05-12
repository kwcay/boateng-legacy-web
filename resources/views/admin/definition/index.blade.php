@extends('layouts.admin')

@section('body')

    <h1>
        Definitions
    </h1>

    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.index') }}">Administration</a>
        </li>
        <li class="active">
            Definitions

            <span style="margin: 0 10px">
                &rarr;
            </span>
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

    <table class="table table-striped table-hover text-center">
        <thead>
            <tr>
                <td>ID</td>
                <td>Title</td>
                <td>Language</td>
                <td>Rating</td>
                <td title="Date on which definition was added to database">
                    Date
                </td>
            </tr>
        </thead>

        <tbody>
            @foreach ($paginator as $definition)
            <tr>
                <td class="text-muted" title="# {{ $definition->id }}" style="cursor: normal">
                    {{ $definition->uniqueId }}
                </td>
                <td>
                    <a href="#">
                        {{ $definition->titles->implode('title', ', ') }}
                    </a>
                </td>
                <td>
                    {{ $definition->languages->implode('name', ', ') }}
                </td>
                <td>
                    {{ $definition->rating }}
                </td>
                <td>
                    {{ date('M Y', strtotime($definition->createdAt)) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination links --}}
    @include('admin.partials.pagination')
@stop
