@extends('layouts.admin')

@section('body')

    <h1>Definition tags</h1>

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
                <td>title</td>
            </tr>
        </thead>

        <tbody>
            @foreach ($paginator as $tag)
            <tr>
                <td class="text-muted" title="# {{ $tag->id }}">
                    {{ $tag->uniqueId }}
                </td>
                <td>
                    <a href="#">
                        {{ $tag->title }}
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination links --}}
    @include('admin.partials.pagination')
@stop
