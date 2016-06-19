@extends('layouts.admin-index')

@section('page-title', 'Tags')

@section('data')

    <table class="table table-striped table-hover text-center">
        <thead>
            <tr>
                <td></td>
                <td>Title</td>
                <td># of definitions</td>
            </tr>
        </thead>

        <tbody>
            @foreach ($paginator as $tag)
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
                                        data-clipboard-text="{{ $tag->uniqueId }}">

                                        <span class="fa fa-clipboard"></span>

                                        id:
                                        <b>
                                            {{ $tag->uniqueId }}
                                            ({{ $tag->id }})
                                        </b>
                                    </a>
                                </li>
                                <li role="separator" class="divider"></li>
                                <li>
                                    <a href="{{ $tag->editUriAdmin }}">
                                        Edit
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="javascript:;"
                                        onclick='return window.deleteRes("tag", "{{ $tag->uniqueId }}", "{{ $tag->title }}")'
                                        class="bg-danger">

                                        Delete
                                    </a>
                                </li>
                            </ul>
                        </div>

                        {{-- View tag --}}
                        <div class="btn-group">
                            <a class="btn btn-default" href="{{ $tag->uri }}" target="_blank">
                                <span class="fa fa-fw fa-external-link"></span>
                            </a>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="edit-res">
                        <a href="{{ $tag->editUriAdmin }}" class="fa fa-pencil"></a>
                    </span>

                    <a href="{{ $tag->editUriAdmin }}">
                        {{ $tag->title }}
                    </a>
                </td>
                <td>
                    {{ $tag->definitionCount }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

@stop
