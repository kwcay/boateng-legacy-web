@extends('admin.layouts.index')

@section('page-title', 'Alphabets')

@section('data')

    <div class="row">
        <div class="col-md-12 text-center">
            <span class="fa fa-fw fa-download"></span>
            Export dataset as
            <a href="{{ route('export.resource', ['resource' => 'alphabet', 'format' => 'json']) }}">
                .json
            </a>
            or
            <a href="{{ route('export.resource', ['resource' => 'alphabet', 'format' => 'yaml']) }}">
                .yaml
            </a>
        </div>
    </div>
    <br>

    <table class="table table-striped table-hover text-center">
        <thead>
            <tr>
                <td></td>
                <td>Name</td>
                <td>Transliteration</td>
                <td title="Alphabet code based on ISO 15924 standard for scripts">
                    Code
                </td>
            </tr>
        </thead>

        <tbody>
            @foreach ($paginator as $alphabet)
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
                                        data-clipboard-text="{{ $alphabet->uniqueId }}">

                                        <span class="fa fa-clipboard"></span>

                                        id:
                                        <b>
                                            {{ $alphabet->uniqueId }}
                                            ({{ $alphabet->id }})
                                        </b>
                                    </a>
                                </li>
                                <li class="info">
                                    <a
                                        href="javascript:;"
                                        class="ctrl-c"
                                        data-clipboard-text="{{ $alphabet->scriptCode }}">

                                        <span class="fa fa-clipboard"></span>

                                        script code:
                                        <b>
                                            {{ $alphabet->scriptCode }}
                                        </b>
                                    </a>
                                </li>
                                <li class="info">
                                    <a
                                        href="javascript:;"
                                        class="ctrl-c"
                                        data-clipboard-text="{{ $alphabet->createdAt }}">

                                        <span class="fa fa-clipboard"></span>

                                        added on:
                                        <b>
                                            {{ date('M j, Y', strtotime($alphabet->createdAt)) }}
                                        </b>
                                    </a>
                                </li>
                                <li role="separator" class="divider"></li>
                                @if ($alphabet->deletedAt)
                                <li>
                                    <a
                                        href="javascript:;"
                                        onclick="return false"
                                        class="bg-warning">

                                        Restore
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="javascript:;"
                                        onclick="return false"
                                        class="bg-danger">

                                        Delete for good
                                    </a>
                                </li>
                                @else
                                <li>
                                    <a href="{{ $alphabet->editUriAdmin }}">
                                        Edit
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="javascript:;"
                                        onclick='return window.deleteRes("alphabet", "{{ $alphabet->uniqueId }}", "{{ $alphabet->name }}")'
                                        class="bg-danger">

                                        Delete
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </td>
                <td>
                    @if ($alphabet->deletedAt)
                        <del
                            class="text-danger"
                            title="Deleted on {{ date('M j, Y', strtotime($alphabet->deletedAt)) }}">

                            {{ $alphabet->name }}
                        </del>
                    @else
                        <span class="edit-res">
                            <a href="{{ $alphabet->editUriAdmin }}" class="fa fa-pencil"></a>
                        </span>

                        <a href="{{ $alphabet->editUriAdmin }}">
                            {{ $alphabet->name }}
                        </a>
                    @endif
                </td>
                <td>
                    @if ($alphabet->deletedAt)
                        <span class="text-danger">
                            {{ $alphabet->transliteration }}
                        </span>
                    @else
                        {{ $alphabet->transliteration }}
                    @endif
                </td>
                <td>
                    <span class="ctrl-c" data-clipboard-text="{{ $alphabet->code }}">
                        <span class="edit-res">
                            <span class="fa fa-fw fa-clipboard"></span>
                        </span>

                        {{ $alphabet->code }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

@stop
