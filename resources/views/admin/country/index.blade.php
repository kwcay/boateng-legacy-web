@extends('layouts.admin-index')

@section('page-title', 'Countries')

@section('data')

    <div class="row">
        <div class="col-md-12 text-center">
            <span class="fa fa-fw fa-download"></span>
            Export dataset as
            <a href="{{ route('admin.export', ['resource' => 'country', 'format' => 'json']) }}">
                .json
            </a>
            or
            <a href="{{ route('admin.export', ['resource' => 'country', 'format' => 'yaml']) }}">
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
                <td>Alternate names</td>
                <td title="Country code based on ISO 3166-1 (alpha-2) standard">
                    Code
                </td>
            </tr>
        </thead>

        <tbody>
            @foreach ($paginator as $country)
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
                                        data-clipboard-text="{{ $country->uniqueId }}">

                                        <span class="fa fa-clipboard"></span>

                                        id:
                                        <b>
                                            {{ $country->uniqueId }}
                                            ({{ $country->id }})
                                        </b>
                                    </a>
                                </li>
                                <li class="info">
                                    <a
                                        href="javascript:;"
                                        class="ctrl-c"
                                        data-clipboard-text="{{ $country->createdAt }}">

                                        <span class="fa fa-clipboard"></span>

                                        added on:
                                        <b>
                                            {{ date('M j, Y', strtotime($country->createdAt)) }}
                                        </b>
                                    </a>
                                </li>
                                <li role="separator" class="divider"></li>
                                @if ($country->deletedAt)
                                <li>
                                    <a
                                        href="javascript:;"
                                        onclick='return window.restore("country", "{{ $country->uniqueId }}", "{{ $country->name }}")'
                                        class="bg-warning">

                                        Restore
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="javascript:;"
                                        onclick='return window.forceDelete("country", "{{ $country->uniqueId }}", "{{ $country->name }}")'
                                        class="bg-danger">

                                        Delete for good
                                    </a>
                                </li>
                                @else
                                <li>
                                    <a href="{{ $country->editUriAdmin }}">
                                        Edit
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="javascript:;"
                                        onclick='return window.trash("country", "{{ $country->uniqueId }}", "{{ $country->name }}")'
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
                    <span class="edit-res">
                        <a href="{{ $country->editUriAdmin }}" class="fa fa-pencil"></a>
                    </span>

                    <a href="{{ $country->editUriAdmin }}">
                        {{ $country->name }}
                    </a>
                </td>
                <td>
                    {{ $country->altNames }}
                </td>
                <td>
                    <span class="ctrl-c" data-clipboard-text="{{ $country->code }}">
                        <span class="edit-res">
                            <span class="fa fa-fw fa-clipboard"></span>
                        </span>

                        {{ $country->code }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

@stop
