@extends('layouts.base')

@section('body')
	@include('partials.header')

	<section>
		<h1>Di Nkɔmɔ</h1>
        <h2 class="center" style="font-weight: normal;">~ The book of Native tongues ~</h2>

        Di Nkɔmɔ is a little project I started in 2014 to help myself learn
        <a href="twi">Twi</a>, my native language. It quickly became a dictionary web app, which I
        hope will be useful to others some day. Di Nkɔmɔ is <a href="twi">Twi</a> for chat,
        or converse.
        <br /><br />

        My name is <a href="http://frnk.ca" target="_blank">Yaw Amankrah</a>. I built this app using
        <a href="http://laravel.com" target="_blank">Laravel</a> and other free resources such as:

        <ul>
            <li>
                <a href="http://www.iso.org/iso/catalogue_detail?csnumber=39534" target="_blank">ISO 639-3</a>
                codes for the representation of names of languages.
            </li>
            <li>
                The <a href="http://www.ethnologue.com/" target="_blank">Ethnologue's</a>
                extensive catalog.
            </li>
            <li>
                The <a href="http://glottolog.org/" target="_blank">Glottolog's</a>
                extensive catalog.
            </li>
            <li>
                <a href="http://cldr.unicode.org" target="_blank">CLDR</a>, or Unicode's Common
                Locale Data Repository.
            </li>
        </ul>
        <br />

        I've spent a lot of time on this app. If you have any thoughts you'd like to share,
        <a href="mailto:&#102;&#114;&#97;&#110;&#107;&#64;&#102;&#114;&#110;&#107;&#46;&#99;&#97;">please do</a> !

	</section>

	@include('partials.footer')
@stop
