@extends('layouts.admin')

@section('body')

    <h1>
        @yield('page-title', 'Resource listing')
    </h1>

    <ol class="breadcrumb">
        <li>
            <a href="{{ route('admin.index') }}">Administration</a>
        </li>
        <li class="active">
            @yield('page-title', 'Resource Index')
        </li>
    </ol>

    {{-- Pagination links --}}
    @include('admin.partials.pagination')

    {{-- Query parameters --}}
    <div class="emphasis">
        @include('admin.partials.query-params')
    </div>
    <br>

    @yield('data')

    {{-- Query parameters --}}
    @if ($total >= 10)
        <div class="emphasis">
            @include('admin.partials.query-params')
        </div>
    @endif

    {{-- Pagination links --}}
    @include('admin.partials.pagination')

    {{-- Delete dialog --}}
    <div class="dialog trash">
        <div>
            <a href="#" class="close">&#10005;</a>
            <h1>Are you sure?</h1>
            <div class="center">
                Are you sure you want to delete
                <h2>&ldquo; <span class="res-name"></span> &rdquo;</h2>
                for ever and ever?
                <br><br>

                <form
                    class="form"
                    method="post"
                    name="trashForm"
                    action="/home">

                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="return" value="{{ Request::input('return', 'admin') }}">
                    <input type="submit" name="confirm" value="yes">
                    <input type="button" name="cancel" value="no, return" onclick="return Dialogs.close()">
                </form>
            </div>
        </div>
    </div>

    {{-- Restore dialog --}}
    <div class="dialog restore">
        <div>
            <a href="#" class="close">&#10005;</a>
            <h1>
                Restore <span class="res-name"></span>?
            </h1>
            <div class="center">
                Are you sure you want to restore
                <h2>&ldquo; <span class="res-name"></span> &rdquo;</h2>
                <br><br>

                <form
                    class="form"
                    method="post"
                    name="restoreForm"
                    action="/home">

                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="return" value="{{ Request::input('return', 'admin') }}">
                    <input type="submit" name="confirm" value="yes">
                    <input type="button" name="cancel" value="no, return" onclick="return Dialogs.close()">
                </form>
            </div>
        </div>
    </div>

    {{-- Force delete dialog --}}
    <div class="dialog force">
        <div>
            <a href="#" class="close">&#10005;</a>
            <h1>
                Permanently delete <span class="res-name"></span>?
            </h1>
            <div class="center">
                Are you sure you want to permanently delete
                <h2>&ldquo; <span class="res-name"></span> &rdquo;</h2>
                (this action is irreversible)
                <br>
                <br>

                <form
                    class="form"
                    method="post"
                    name="forceDeleteForm"
                    action="/home">

                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="return" value="{{ Request::input('return', 'admin') }}">
                    <input type="submit" name="confirm" value="I'm sure">
                    <input type="button" name="cancel" value="no, return" onclick="return Dialogs.close()">
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">

        // Deletes a resource.
        window.trash = function(res, id, name) {

            // Update delete confirmation form.
            $('.dialog.trash .res-name').html(name);
            $('.dialog.trash input[name="confirm"]').val('yes, delete ' + name);
            document.trashForm.action = App.root + 'r/' + res + '/' + id;

            Dialogs.open('trash');

            return false;
        };

        // Restores a resource.
        window.restore = function(res, id, name) {

            // Update restore confirmation form.
            $('.dialog.restore .res-name').html(name);
            $('.dialog.restore input[name="confirm"]').val('yes, restore ' + name);
            document.restoreForm.action = App.root + 'r/' + res + '/' + id + '/restore';

            Dialogs.open('restore');

            return false;
        };

        // Permanently deletes a resource.
        window.forceDelete = function(res, id, name) {

            // Update restore confirmation form.
            $('.dialog.force .res-name').html(name);
            document.forceDeleteForm.action = App.root + 'r/' + res + '/' + id + '/commit';

            Dialogs.open('force');

            return false;
        };

        //
        $('.ctrl-c').copyOnClick();

    </script>
@stop
