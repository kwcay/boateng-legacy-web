@extends('layouts.admin')

@section('body')

    <h1>Definitions</h1>

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
            </tr>
        </thead>

        <tbody>
            @foreach ($paginator as $definition)
            <tr>
                <td>
                    <div class="btn-group">

                        {{-- Checkbox --}}
                        <button
                            type="button"
                            class="btn btn-default">

                            <span class="fa fa-square-o fa-fw"></span>
                        </button>

                        {{-- Extra info --}}
                        <div class="btn-group">
                            <button
                                type="button"
                                class="btn btn-default dropdown-toggle"
                                data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">

                                <span class="fa fa-info fa-fw"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            id:
                                            <b>
                                                {{ $definition->uniqueId }}
                                                ({{ $definition->id }})
                                            </b>
                                        </li>
                                        <li class="list-group-item">
                                            type:
                                            <b>
                                                {{ $definition->subType }}
                                            </b>
                                        </li>
                                        <li class="list-group-item">
                                            rating:
                                            <b>
                                                {{ $definition->rating }}
                                            </b>
                                        </li>
                                        <li class="list-group-item">
                                            language:
                                            <b>
                                                {{ $definition->languages->implode('name', ', ') }}
                                            </b>
                                        </li>
                                        <li class="list-group-item">
                                            added on:
                                            <b>
                                                {{ date('M j, Y', strtotime($definition->createdAt)) }}
                                            </b>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>

                        {{-- Admin actions --}}
                        <div class="btn-group">
                            <button
                                type="button"
                                class="btn btn-default dropdown-toggle"
                                data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">

                                <span class="fa fa-cog fa-fw"></span>
                            </button>
                            <ul class="dropdown-menu">
                                @if ($definition->deletedAt)
                                <li>
                                    <a href="#">
                                        Restore
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        Force delete
                                    </a>
                                </li>
                                @else
                                <li>
                                    <a href="{{ $definition->uri }}" target="_blank">
                                        View
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.definition.edit', ['id' => $definition->uniqueId, 'return' => 'admin']) }}">
                                        Edit
                                    </a>
                                </li>
                                <li role="separator" class="divider"></li>
                                <li>
                                    <a href="#">
                                        Delete
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </td>
                <td>
                    @if ($definition->deletedAt)
                        <del title="Deleted on {{ date('m j, Y', strtotime($definition->deletedAt)) }}">
                            {{ $definition->titles->implode('title', ', ') }}
                        </del>
                    @else
                        <span title="Added on {{ date('m j, Y', strtotime($definition->createdAt)) }}">
                            {{ $definition->titles->implode('title', ', ') }}
                        </span>
                    @endif
                </td>
                <td>
                    <a
                        href="{{ route('admin.definition.edit', ['id' => $definition->uniqueId, 'return' => 'admin']) }}">
                        {{ $definition->getPracticalTranslation('eng') }}
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination links --}}
    @include('admin.partials.pagination')
@stop
