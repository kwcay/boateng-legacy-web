@extends('layouts.narrow')

@section('body')

	<section>
		<h1>
            Where to?
        </h1>

        <ul class="fa-ul">
            <li>
                <i class="fa fa-li fa-search"></i>
                <a href="{{ route('home') }}">
                    Lookup something
                </a>
            </li>
            <li>
                <i class="fa fa-li fa-plus"></i>
                <a href="{{ route('contribute') }}">
                    Add to Di Nkɔmɔ
                </a>
            </li>
            <li>
                <i class="fa fa-li fa-caret-down"></i>
                <a href="{{ route('about') }}">
                    About this app
                </a>

                <ul class="fa-ul">
                    <li>
                        <i class="fa fa-li fa-angle-right"></i>
                        <a href="{{ route('story') }}">
                            Story of Di Nkɔmɔ
                        </a>
                    </li>
                    <li>
                        <i class="fa fa-li fa-angle-right"></i>
                        <a href="{{ route('stats') }}">
                            Di Nkɔmɔ in numbers
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <i class="fa fa-li fa-caret-down"></i> Languages

                <ul class="fa-ul">
                    @foreach ($languages as $lang)
                        <li>
                            <i class="fa fa-li fa-angle-right"></i>
                            <a href="{{ $lang->uri }}">
                                {{ $lang->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
            <li>
                <i class="fa fa-li fa-exclamation"></i>
                <a href="{{ route('definition.random') }}">
                    Surprise me
                </a>
            </li>
        </ul>
	</section>

@stop
