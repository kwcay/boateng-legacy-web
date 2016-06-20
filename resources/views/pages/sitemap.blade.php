@extends('layouts.narrow')

@section('title', 'Sitemap - Di Nkɔmɔ')

@section('body')

	<section>
		<h1>
            Where to?
        </h1>

        <ul class="fa-ul">
            <li>
                <i class="fa fa-li fa-search"></i>
                <a href="{{ route('home') }}">
                    Di Nkɔmɔ reference
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
                        <a href="{{ route('about.story') }}">
                            Story of Di Nkɔmɔ
                        </a>
                    </li>
                    <li>
                        <i class="fa fa-li fa-angle-right"></i>
                        <a href="{{ route('about.stats') }}">
                            Di Nkɔmɔ in numbers
                        </a>
                    </li>
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
