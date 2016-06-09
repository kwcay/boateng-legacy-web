@extends('admin.layouts.index')

@section('page-title', 'Alphabets')

@section('data')

    <table class="table table-striped table-hover text-center">
        <thead>
            <tr>
                <td>ID</td>
                <td>Name</td>
                <td title="Alphabet code based on ISO 15924 standard for scripts">
                    Code
                </td>
                <td title="Date on which alphabet was added to database">
                    Date
                </td>
            </tr>
        </thead>

        <tbody>
            @foreach ($paginator as $alphabet)
            <tr>
                <td class="text-muted" title="# {{ $alphabet->id }}">
                    {{ $alphabet->uniqueId }}
                </td>
                <td>
                    <a href="#">
                        {{ $alphabet->name }}
                    </a>
                </td>
                <td>
                    {{ $alphabet->code }}
                </td>
                <td>
                    {{ date('M Y', strtotime($alphabet->createdAt)) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

@stop
