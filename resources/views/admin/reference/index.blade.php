@extends('layouts.admin-index')

@section('page-title', 'References')

@section('data')

    <div class="row">
        <div class="col-md-12 text-center">
            <span class="fa fa-fw fa-download"></span>
            Export dataset as
            <a href="{{ route('export.resource', ['resource' => 'reference', 'format' => 'json']) }}">
                .json
            </a>
            or
            <a href="{{ route('export.resource', ['resource' => 'reference', 'format' => 'yaml']) }}">
                .yaml
            </a>
        </div>
    </div>
    <br>

    <table class="table table-striped table-hover text-center">
        <thead>
            <tr>
                <td></td>
                <td>Short name</td>
                <td>type</td>
            </tr>
        </thead>

        <tbody>
            @foreach ($paginator as $reference)
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
                                        data-clipboard-text="{{ $reference->uniqueId }}">

                                        <span class="fa fa-clipboard"></span>

                                        id:
                                        <b>
                                            {{ $reference->uniqueId }}
                                            ({{ $reference->id }})
                                        </b>
                                    </a>
                                </li>
                                <li role="separator" class="divider"></li>
                                @if ($reference->deletedAt)
                                <li>
                                    <a
                                        href="javascript:;"
                                        onclick='return window.restore("reference", "{{ $reference->uniqueId }}", "{{ $reference->name }}")'
                                        class="bg-warning">

                                        Restore
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="javascript:;"
                                        onclick='return window.forceDelete("reference", "{{ $reference->uniqueId }}", "{{ $reference->name }}")'
                                        class="bg-danger">

                                        Delete for good
                                    </a>
                                </li>
                                @else
                                <li>
                                    <a href="{{ $reference->editUriAdmin }}">
                                        Edit
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="javascript:;"
                                        onclick='return window.trash("reference", "{{ $reference->uniqueId }}", "{{ $reference->name }}");'
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
                    @if ($reference->deletedAt)
                        <del
                            class="text-danger"
                            title="Deleted on {{ date('M j, Y', strtotime($reference->deletedAt)) }}">

                            {{ $reference->shortName }}
                        </del>
                    @else
                        <span class="edit-res">
                            <a href="{{ $reference->editUriAdmin }}" class="fa fa-pencil"></a>
                        </span>

                        <a href="{{ $reference->editUriAdmin }}">
                            {{ $reference->shortName }}
                        </a>
                    @endif
                </td>
                <td>
                    <span class="ctrl-c" data-clipboard-text="{{ $reference->type }}">
                        <span class="edit-res">
                            <span class="fa fa-fw fa-clipboard"></span>
                        </span>

                        {{ $reference->type }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

@stop
