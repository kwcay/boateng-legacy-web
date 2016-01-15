@extends('layouts.narrow')

@section('body')

	<section>
		<h1>
            Where to?
        </h1>

        <ul>
            <li>
                <a href="{{ route('home') }}">
                    Reference lookup
                </a>
            </li>
            <li>
                Languages

                <ul>
                    @foreach ($languages as $lang)
                        <li>
                            <a href="{{ $lang->uri }}">
                                {{ $lang->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        </ul>
	</section>

@stop
