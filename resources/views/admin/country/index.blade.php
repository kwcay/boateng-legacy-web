@extends('admin.layouts.index')

@section('page-title', 'Countries')

@section('data')

    <table class="table table-striped table-hover text-center">
        <thead>
            <tr>
                <td>ID</td>
                <td>Name</td>
                <td title="Country code based on ISO 3166-1 (alpha-2) standard">
                    Code
                </td>
            </tr>
        </thead>

        <tbody>
            @foreach ($paginator as $country)
            <tr>
                <td class="text-muted" title="# {{ $country->id }}">
                    {{ $country->uniqueId }}
                </td>
                <td>
                    <a href="#">
                        {{ $country->name }}
                    </a>
                </td>
                <td>
                    {{ $country->code }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
@stop
