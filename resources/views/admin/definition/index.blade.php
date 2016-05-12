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
                <td></td>
                <td>Title</td>
                <td>Translation</td>
                <td>Type</td>
            </tr>
        </thead>

        <tbody>
            @foreach ($paginator as $definition)
            <tr>
                <td>
                    <span class="fa fa-square-o"></span>
                </td>
                <td>
                    <a href="{{ route('admin.definition.edit', ['id' => $definition->uniqueId, 'next' => 'admin']) }}">
                        {{ $definition->titles->implode('title', ', ') }}
                    </a>
                </td>
                <td>
                    {{ $definition->getPracticalTranslation('eng') }}
                </td>
                <td>
                    {{ $definition->subType }}
                    ({{ $definition->type }})
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination links --}}
    @include('admin.partials.pagination')
@stop
