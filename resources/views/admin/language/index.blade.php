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
            <a href="{{ route('admin.tag.index') }}">
                Tags
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
                <td>Name</td>
                <td title="Language code based on ISO-639-3 standard">
                    Code
                </td>
            </tr>
        </thead>

        <tbody>
            @foreach ($paginator as $language)
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
                                                {{ $language->uniqueId }}
                                                ({{ $language->id }})
                                            </b>
                                        </li>
                                        <li class="list-group-item">
                                            added on:
                                            <b>
                                                {{ date('M j, Y', strtotime($language->createdAt)) }}
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
                                @if ($language->deletedAt)
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
                                    <a href="{{ $language->uri }}" target="_blank">
                                        View
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.language.edit', ['id' => $language->uniqueId, 'return' => 'admin']) }}">
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
                    <a href="#">
                        {{ $language->name }}
                    </a>
                </td>
                <td>
                    {{ $language->code }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Pagination links --}}
    @include('admin.partials.pagination')
@stop
