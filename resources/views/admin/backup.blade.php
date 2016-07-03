@extends('layouts.narrow')

@section('body')

	<h1>
	    Backups
    </h1>

    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.index') }}">Administration</a>
        </li>
        <li class="active">
            Backups
        </li>
    </ol>

    {{-- Pagination links --}}
    @include('partials.pagination')

    {{-- Query parameters --}}
    <div class="emphasis">
        @include('partials.query-params')
    </div>
    <br>

    <table class="table table-striped table-hover text-center">
        <thead>
            <tr>
                <td></td>
                <td>Date</td>
                <td>Name</td>
                <td>Size</td>
                <td>Type</td>
            </tr>
        </thead>

        <tbody>
            @foreach ($paginator as $file)
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
                                <li>
                                    <a
                                        href="javascript:;"
                                        onclick='return window.restore("{{ $file['name'] .'.'. $file['ext'] }}")'
                                        class="bg-warning">

                                        Restore
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="javascript:;"
                                        onclick='return window.trash("{{ $file['name'] .'.'. $file['ext'] }}")'
                                        class="bg-danger">

                                        Delete
                                    </a>
                                </li>
                            </ul>
                        </div>

                        {{-- Download backup --}}
                        <div class="btn-group">
                            <a class="btn btn-default" href="javascript:;">
                                <span class="fa fa-fw fa-download"></span>
                            </a>
                        </div>
                    </div>
                </td>
                <td>
                    {{ $file['date'] }}
                </td>
                <td>
                    {{ $file['name'] }}
                </td>
                <td>
                    {{ $file['size'] }}
                </td>
                <td>
                    {{ $file['ext'] }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Query parameters --}}
    @if ($total >= 10)
        <div class="emphasis">
            @include('partials.query-params')
        </div>
    @else
        <br>
    @endif

    {{-- Pagination links --}}
    @include('partials.pagination')

	<h2>Upload a backup file</h2>

    <form
        class="form upload"
        enctype="multipart/form-data"
        method="post"
        action="{{ route('admin.backup.upload') }}">
		{!! csrf_field() !!}

	    <input type="file" name="file">
	    <br>

	    <input type="submit" value="Upload">
	</form>

@stop
