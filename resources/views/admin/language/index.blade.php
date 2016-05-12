@extends('layouts.admin')

@section('body')

    <h1>Languages</h1>

    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.index') }}">Administration</a>
        </li>
        <li class="active">
            Languages

            <span style="margin: 0 10px">
                &rarr;
            </span>
            <a href="{{ route('admin.definition.index') }}">
                Definitions
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
                <td>Name</td>
                <td title="Language code based on ISO-639-3 standard">
                    Code
                </td>
                <td title="Date on which language was added to database">
                    Date
                </td>
            </tr>
        </thead>

        <tbody>
            @foreach ($paginator as $language)
            <tr>
                <td class="text-muted" title="# {{ $language->id }}">
                    {{ $language->uniqueId }}
                </td>
                <td>
                    <a href="#">
                        {{ $language->name }}
                    </a>
                </td>
                <td>
                    {{ $language->code }}
                </td>
                <td>
                    {{ date('M Y', strtotime($language->createdAt)) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination links --}}
    @include('admin.partials.pagination')
@stop
