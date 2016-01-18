@extends('layouts.narrow')

@section('body')

	<section>
		<h1>What would you like to do?</h1>

        <ul class="fa-ul">
            <li>
                Thanks for asking. I'd like to
                <a href="{{ route('language.create') }}">suggest a new language</a>.
            </li>
            <li>
                Thanks for asking. I'd like to suggest a new definition.
            </li>
            <li>
                Thanks for asking. I'd like to
                <a href="mailto:&#102;&#114;&#97;&#110;&#107;&#64;&#102;&#114;&#110;&#107;&#46;&#99;&#97;">
                    share some feedback
                </a> or say nice things to the author.
            </li>
            <li>
                Thanks for asking. <a href="{{ route('about') }}">I don't know</a>.
            </li>
        </ul>
	</section>

@stop
