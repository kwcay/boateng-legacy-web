@extends('layouts.admin')

@section('body')

    <h1>Countries</h1>

    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.index') }}">Administration</a>
        </li>
        <li class="active">
            Countries

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
        </li>
    </ol>

    {{-- Pagination links --}}
    @include('admin.partials.pagination')

    <table class="table table-striped table-hover text-center">
        <thead>
            <tr>
                <td>ID</td>
                <td>Name</td>
                <td title="Country code based on ISO 3166-1 (alpha-2) standard">
                    Code
                </td>
            </tr>
        </thead>

        <tbody>
            @foreach ($paginator as $country)
            <tr>
                <td class="text-muted" title="# {{ $country->id }}">
                    {{ $country->uniqueId }}
                </td>
                <td>
                    <a href="#">
                        {{ $country->name }}
                    </a>
                </td>
                <td>
                    {{ $country->code }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination links --}}
    @include('admin.partials.pagination')
@stop
