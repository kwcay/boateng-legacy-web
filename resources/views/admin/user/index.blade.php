@extends('layouts.admin-index')

@section('page-title', 'Users')

@section('data')

    <div class="row">
        <div class="col-md-12 text-center">
            <span class="fa fa-fw fa-download"></span>
            Export dataset as
            <a href="{{ route('export.resource', ['resource' => 'user', 'format' => 'json']) }}">
                .json
            </a>
            or
            <a href="{{ route('export.resource', ['resource' => 'user', 'format' => 'yaml']) }}">
                .yaml
            </a>
        </div>
    </div>
    <br>

    <table class="table table-striped table-hover text-center">
        <thead>
            <tr>
                <td></td>
                <td>Email</td>
                <td>Name</td>
            </tr>
        </thead>

        <tbody>
            @foreach ($paginator as $user)
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
                                        data-clipboard-text="{{ $user->uniqueId }}">

                                        <span class="fa fa-clipboard"></span>

                                        id:
                                        <b>
                                            {{ $user->uniqueId }}
                                            ({{ $user->id }})
                                        </b>
                                    </a>
                                </li>
                                <li class="info">
                                    <a
                                        href="javascript:;"
                                        class="ctrl-c"
                                        data-clipboard-text="{{ $user->createdAt }}">

                                        <span class="fa fa-clipboard"></span>

                                        signed up on:
                                        <b>
                                            {{ date('M j, Y', strtotime($user->createdAt)) }}
                                        </b>
                                    </a>
                                </li>
                                <li role="separator" class="divider"></li>
                                @if ($user->deletedAt)
                                <li>
                                    <a
                                        href="javascript:;"
                                        onclick='return window.restore("language", "{{ $user->uniqueId }}", "{{ $user->name }}")'
                                        class="bg-warning">

                                        Restore
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="javascript:;"
                                        onclick='return window.forceDelete("user", "{{ $user->uniqueId }}", "{{ $user->name }}")'
                                        class="bg-danger">

                                        Delete for good
                                    </a>
                                </li>
                                @else
                                <li>
                                    <a href="{{ $user->uri }}" target="_blank">
                                        View
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ $user->editUriAdmin }}">
                                        Edit
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="javascript:;"
                                        onclick='return window.trash("user", "{{ $user->uniqueId }}", "{{ $user->name }}")'
                                        class="bg-danger">

                                        Delete
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>

                        {{-- View user profile --}}
                        <div class="btn-group">
                            <a class="btn btn-default" href="{{ $user->uri }}" target="_blank">
                                <span class="fa fa-fw fa-external-link"></span>
                            </a>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="ctrl-c" data-clipboard-text="{{ $user->email }}">
                        <span class="edit-res">
                            <span class="fa fa-fw fa-clipboard"></span>
                        </span>
                    </span>
                    <span class="edit-res">
                        <a href="{{ $user->editUriAdmin }}" class="fa fa-pencil"></a>
                    </span>

                    <a href="{{ $user->editUriAdmin }}">
                        {{ $user->email }}
                    </a>
                </td>
                <td>
                    @if (strlen($user->name))
                        <span class="ctrl-c" data-clipboard-text="{{ $user->name }}">
                            <span class="edit-res">
                                <span class="fa fa-fw fa-clipboard"></span>
                            </span>

                            {{ $user->name }}
                        </span>
                    @else
                        ---
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

@stop
