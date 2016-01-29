@extends('layouts.narrow')

@section('title', 'How Di Nkomo Started')

@section('body')

	<section>
        <h1>
            Di Nkɔmɔ
            <br>

            <small>The Story</small>
        </h1>

        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 text-center">
                I started Di Nkɔmɔ in 2014 to help myself learn <a href="/twi">Twi</a>,
                my native language. With time, I realized that Twi has hidden gems that are not
                directly translatable into English. As do many other languages, they require
                context&mdash;a cultural context. And so, in the hopes of capturing and documenting
                these cultural gems, Di Nkɔmɔ has grown from just under 100 Twi words to
                <a href="{{ route('stats') }}">{{ $defs }} definitions in {{ $langs }} languages</a>.
            </div>
        </div>
        <br>
        <br>

        <h2 class="text-center">
            Timeline
        </h2>

        <div class="timeline">

            <!--You -->
            <div class="event">
                <div class="event-marker"></div>

                <div class="event-details">
                    <h1>You</h1>
                    <p>Thanks for checking out the app !</p>
                    <span class="date">{{ date('M Y') }} <small>i.e. right now</small></span>
                </div>
            </div>

            <!-- Ken Ya Innovate -->
            <div class="event">
                <div class="event-marker"></div>

                <div class="event-details">
                    <h1>Innovation Challenge</h1>
                    <p>Hearing other people's ideas at the Ken_Ya Innovate contest serves as inspiration.</p>
                    <span class="date">June 2015</span>
                </div>
            </div>

            <!-- Trip to Ghana -->
            <div class="event">
                <div class="event-marker"></div>

                <div class="event-details">
                    <h1>Trip to Ghana</h1>
                    <p>Learning Dagbani, idea expands to include words and concepts from other languages.</p>
                    <span class="date">May 2014</span>
                </div>
            </div>

            <!-- Birth of idea -->
            <div class="event">
                <div class="event-marker"></div>

                <div class="event-details">
                    <h1>Birth of Idea</h1>
                    <p>An online Twi dictionary to practice and learn new words.</p>
                    <span class="date">Late 2013</span>
                </div>
            </div>
        </div>
        <br>

        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 text-center">
                <a href="/twi/di_nkɔmɔ">Di Nkɔmɔ</a> is Twi for chat, or converse. I've spent a lot of time
                on this app. If you have any thoughts you'd like to share,
                <a href="mailto:&#102;&#114;&#97;&#110;&#107;&#64;&#102;&#114;&#110;&#107;&#46;&#99;&#97;">please do</a> !
            </div>
        </div>
	</section>

@stop
