@extends('admin.layouts.index')

@section('page-title', 'Definition tags')

@section('data')

    <table class="table table-striped table-hover text-center">
        <thead>
            <tr>
                <td>ID</td>
                <td>title</td>
            </tr>
        </thead>

        <tbody>
            @foreach ($paginator as $tag)
            <tr>
                <td class="text-muted" title="# {{ $tag->id }}">
                    {{ $tag->uniqueId }}
                </td>
                <td>
                    <a href="#">
                        {{ $tag->title }}
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
@stop
