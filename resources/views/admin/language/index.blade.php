@extends('admin.layouts.index')

@section('page-title', 'Languages')

@section('data')

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
                                <li class="info">
                                    <a
                                        href="javascript:;"
                                        class="ctrl-c"
                                        data-clipboard-text="{{ $language->uniqueId }}">

                                        <span class="fa fa-clipboard"></span>

                                        id:
                                        <b>
                                            {{ $language->uniqueId }}
                                            ({{ $language->id }})
                                        </b>
                                    </a>
                                </li>
                                <li class="info">
                                    <a
                                        href="javascript:;"
                                        class="ctrl-c"
                                        data-clipboard-text="{{ $language->createdAt }}">

                                        <span class="fa fa-clipboard"></span>

                                        added on:
                                        <b>
                                            {{ date('M j, Y', strtotime($language->createdAt)) }}
                                        </b>
                                    </a>
                                </li>
                                <li role="separator" class="divider"></li>
                                @if ($language->deletedAt)
                                <li>
                                    <a href="#">
                                        Restore
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        Delete for good
                                    </a>
                                </li>
                                @else
                                <li>
                                    <a href="{{ $language->uri }}" target="_blank">
                                        View
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ $language->editUriAdmin }}">
                                        Edit
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="javascript:;"
                                        onclick="return window.deleteRes('language', '{{ $language->uniqueId }}', '{{ $language->name }}')"
                                        class="bg-danger">
                                        Delete
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>

                        {{-- View language --}}
                        <div class="btn-group">
                            <a class="btn btn-default" href="{{ $language->uri }}" target="_blank">
                                <span class="fa fa-fw fa-external-link"></span>
                            </a>
                        </div>
                    </div>
                </td>
                <td>
                    @if ($language->deletedAt)
                        <del title="Deleted on {{ date('M j, Y', strtotime($language->deletedAt)) }}">
                            {{ $language->name }}
                        </del>
                    @else
                        <span class="edit-res">
                            <a href="{{ $language->editUriAdmin }}" class="fa fa-pencil"></a>
                        </span>

                        <a href="{{ $language->editUriAdmin }}">
                            {{ $language->name }}
                        </a>
                    @endif
                </td>
                <td>
                    <span class="ctrl-c" data-clipboard-text="{{ $language->code }}">
                        <span class="edit-res">
                            <span class="fa fa-fw fa-clipboard"></span>
                        </span>

                        {{ $language->code }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

@stop
