@extends('layouts.narrow')

@section('body')

	<h1>
	    Backups

        <span class="edit-res">
            <a href="{{ route('admin.backup.create') }}" class="fa fa-plus"></a>
        </span>
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
                                        onclick='return window.restore("{{ $file['name'] .'.'. $file['ext'] }}", {{ $file['timestamp'] }})'
                                        class="bg-warning">

                                        Restore
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href="javascript:;"
                                        onclick='return window.trash("{{ $file['name'] .'.'. $file['ext'] }}", {{ $file['timestamp'] }})'
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

    {{-- Restore dialog --}}
    <div class="dialog restore">
        <div>
            <a href="#" class="close">&#10005;</a>
            <h1>
                Restore backup?
            </h1>
            <div class="center">
                Are you sure you want to restore
                <h2 class="res-name"></h2>

                and all of its contents?
                <br>
                <br>

                <form
                    class="form"
                    method="post"
                    name="restoreForm"
                    action="{{ route('admin.backup.restore', ':id') }}">

                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="timestamp" value="0">
                    <input type="hidden" name="return" value="{{ Request::input('return', 'admin') }}">
                    <input type="submit" name="confirm" value="yes">
                    <input type="button" name="cancel" value="no, return" onclick="return Dialogs.close()">
                </form>
            </div>
        </div>
    </div>

    {{-- Delete dialog --}}
    <div class="dialog trash">
        <div>
            <a href="#" class="close">&#10005;</a>
            <h1>Are you sure?</h1>
            <div class="center">
                Are you sure you want to delete
                <h2 class="res-name"></h2>
                for ever and ever?
                <br><br>

                <form
                    class="form"
                    method="post"
                    name="trashForm"
                    action="{{ route('admin.backup.destroy', ':id') }}">

                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="timestamp" value="0">
                    <input type="hidden" name="return" value="{{ Request::input('return', 'admin') }}">
                    <input type="submit" name="confirm" value="yes">
                    <input type="button" name="cancel" value="no, return" onclick="return Dialogs.close()">
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">

        // Restores a backup file.
        window.restore = function(name, timestamp) {

            // Update restore confirmation form.
            $('.dialog.restore .res-name').html(name);
            $('.dialog.restore input[name="timestamp"]').val(timestamp);
            document.restoreForm.action = App.root + 'admin/backup/restore/' + encodeURIComponent(name);

            Dialogs.open('restore');

            return false;
        };

        // Deletes a backup file.
        window.trash = function(name, timestamp) {

            // Update delete confirmation form.
            $('.dialog.trash .res-name').html(name);
            $('.dialog.trash input[name="timestamp"]').val(timestamp);
            document.trashForm.action = App.root + 'admin/backup/destroy/' + encodeURIComponent(name);

            Dialogs.open('trash');

            return false;
        };

    </script>

@stop
