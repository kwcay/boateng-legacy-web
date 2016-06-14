@extends('admin.layouts.index')

@section('page-title', 'Definitions')

@section('data')

    <div class="row">
        <div class="col-md-12 text-center">
            <span class="fa fa-fw fa-download"></span>
            Export dataset as
            <a href="{{ route('export.resource', ['resource' => 'definition', 'format' => 'json']) }}">
                .json
            </a>
            or
            <a href="{{ route('export.resource', ['resource' => 'definition', 'format' => 'yaml']) }}">
                .yaml
            </a>
        </div>
    </div>
    <br>

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
                                        data-clipboard-text="{{ $definition->uniqueId }}">

                                        <span class="fa fa-clipboard"></span>

                                        id:
                                        <b>
                                            {{ $definition->uniqueId }}
                                            ({{ $definition->id }})
                                        </b>
                                    </a>
                                </li>
                                <li class="info">
                                    <a
                                        href="javascript:;"
                                        class="ctrl-c"
                                        data-clipboard-text="{{ $definition->subType }}">

                                        <span class="fa fa-clipboard"></span>

                                        type:
                                        <b>
                                            {{ $definition->subType }}
                                        </b>
                                    </a>
                                </li>
                                <li class="info">
                                    <a
                                        href="javascript:;"
                                        class="ctrl-c"
                                        data-clipboard-text="{{ $definition->rating }}">

                                        <span class="fa fa-clipboard"></span>

                                        rating:
                                        <b>
                                            {{ $definition->rating }}
                                        </b>
                                    </a>
                                </li>
                                <li class="info">
                                    <a
                                        href="javascript:;"
                                        class="ctrl-c"
                                        data-clipboard-text="{{ $definition->createdAt }}">

                                        <span class="fa fa-clipboard"></span>

                                        added on:
                                        <b>
                                            {{ date('M j, Y', strtotime($definition->createdAt)) }}
                                        </b>
                                    </a>
                                </li>
                                <li role="separator" class="divider"></li>
                                <li>
                                    <a href="{{ $definition->mainLanguage->uri }}" target="_blank">
                                        language:
                                        <b>
                                            {{ $definition->languages->implode('name', ', ') }}
                                        </b>
                                    </a>
                                </li>
                                <li role="separator" class="divider"></li>
                                @if ($definition->deletedAt)
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
                                    <a href="{{ $definition->uri }}" target="_blank">
                                        View
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.definition.edit', ['id' => $definition->uniqueId, 'return' => 'admin']) }}">
                                        Edit
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="javascript:;"
                                        onclick='return window.deleteRes("definition", "{{ $definition->uniqueId }}", "{{ $definition->mainTitle }}")'
                                        class="bg-danger">
                                        Delete
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>

                        {{-- View definition --}}
                        <div class="btn-group">
                            <a class="btn btn-default" href="{{ $definition->uri }}" target="_blank">
                                <span class="fa fa-fw fa-external-link"></span>
                            </a>
                        </div>
                    </div>
                </td>
                <td>
                    @if ($definition->deletedAt)
                        <del
                            class="text-danger"
                            title="Deleted on {{ date('M j, Y', strtotime($definition->deletedAt)) }}">

                            {{ $definition->titles->implode('title', ', ') }}
                        </del>
                    @else
                        <span
                            title="Added on {{ date('M j, Y', strtotime($definition->createdAt)) }}"
                            class="ctrl-c"
                            data-clipboard-text="{{ $definition->titles->implode('title', ', ') }}">

                            <span class="edit-res fa fa-clipboard"></span>

                            {{ $definition->titles->implode('title', ', ') }}
                        </span>
                    @endif
                </td>
                <td>
                    @if ($definition->deletedAt)
                        <span class="text-danger">
                            {{ $definition->getPracticalTranslation('eng') }}
                        </span>
                    @else
                        <span class="edit-res fa fa-pencil"></span>
                        <a
                            href="{{ route('admin.definition.edit', ['id' => $definition->uniqueId, 'return' => 'admin']) }}">
                            {{ $definition->getPracticalTranslation('eng') }}
                        </a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

@stop
