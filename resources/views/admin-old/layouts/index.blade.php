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
            @yield('page-title', 'Resource listing')
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
    <div class="dialog del">
        <div>
            <a href="#" class="close">&#10005;</a>
            <h1>Are you sure?</h1>
            <div class="center">
                Are you sure you want to delete
                <h2>&ldquo; <span class="del-res-name"></span> &rdquo;</h2>
                for ever and ever?
                <br><br>

                <form
                    class="form"
                    method="post"
                    name="deleteRes"
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

    <script type="text/javascript">

        // Deletes a resource.
        window.deleteRes = function(res, id, name) {

            // Update delete confirmation form.
            $('.dialog.del .del-res-name').html(name);
            $('.dialog.del input[name="confirm"]').val('yes, delete ' + name);
            document.deleteRes.action = App.root + 'admin/' + res + '/' + id;

            Dialogs.open('del');

            return false;
        };

        //
        $('.ctrl-c').copyOnClick();

    </script>
@stop
