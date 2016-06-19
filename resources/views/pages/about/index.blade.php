@extends('layouts.narrow')

@section('title', 'About Di Nkɔmɔ')

@section('body')

	<section>
        <h1>
            Di Nkɔmɔ
            <br>

            <small>
                @lang('branding.tag_line')
            </small>
        </h1>

        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 text-center">
                Di Nkɔmɔ is a free, online reference for the cultures of the world.
                <br>
                <br>

                It started as an online dictionary for those languages outside the mainstream
                (95% of all languages
                <a href="{{ route('stats') }}">fall within that category</a>).
                Yet, language by itself doesn't always convey a full thought or meaning. For
                instance, the literal translation of <a href="/twi/Twereduampɔn">Twereduampɔn</a>
                (a <a href="{{ route('language.show', ['code' => 'twi']) }}">Twi</a> word for God)
                is <q>the tree one leans against and does not fall.</q> Doesn't this
                make you wonder what God actually means to the Asante people of West Africa?
                Combined with a cultural context, language has the power of revealing the history
                and spirit of a people.
                <br>
                <br>

                <h2>General goals</h2>
                <ol class="text-left">
                    <li>
                        Discover the history that's hidden in our languages and cultures.
                    </li>
                    <li>
                        Find relations between our cultures.
                    </li>
                    <li>
                        Create a free resource that could be useful to those ends.
                    </li>
                </ol>
                <br>

                Language is, truly, a bearer and medium through which culture is experienced and
                transmitted. <a href="{{ route('story') }}">My hope</a> is that Di Nkɔmɔ can be
                useful in preserving and making sense of these treasures.
            </div>
        </div>
	</section>

@stop
