@extends('layouts.half-hero')

@section('title', 'About - '. trans('branding.title'))

@section('hero')

    <h1>@lang('branding.title')</h1>
    <h4>
        @lang('branding.tag_line')
    </h4>

@stop

@section('body')

	<section>
        <div class="row">
            <div class="col-sm-12">
                <blockquote cite="http://">
                    @lang('branding.title') is a free, online reference for the cultures of the world.
                </blockquote>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="well">
                    <h3>Numbers</h3>

                    ...
                </div>
            </div>
            <div class="col-md-6">
                <div class="well">
                    <h3>General goals</h3>

                    <ol class="text-left">
                        <li>
                            Discover the history that's hidden in our languages and traditions.
                        </li>
                        <li>
                            Find commonalities between our cultures.
                        </li>
                        <li>
                            Create a free resource that could be useful to those ends.
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
                <h3>Story</h3>

                In 2013, Dora Boateng was really a Twi dictionary called Di Nkɔmɔ. It didn't take long to
                realize, however, that Twi has hidden gems that are not directly translatable into English. As do
                many other languages, they require context&mdash;a cultural context. And so, in the hopes of capturing
                and documenting these and many other treasures, Dora Boateng was born.
                <br>
                <br>

                There are over 7,000 languages spoken around the world today, 5% of which are spoken by
                about 95% of the population. In other words, a handful of the world's languages has spread
                to most of the human population. Meanwhile, it is estimated that a language
                becomes <em>extinct</em> every <em>two weeks</em>. In fact, about
                <em>35% are considered threatened</em> or on the road to extinction
                <a href="https://www.ethnologue.com/statistics" target="_blank">according to Ethnologue</a>.
                <br>
                <br>

                Language is, truly, a bearer and medium through which culture is experienced and
                transmitted. It might not be enough to save all these treasures, but the
                {{ App\Utilities\Display::definitionCount() }} definitions in
                {{ App\Utilities\Display::languageCount() }} languages stored in @lang('branding.title') are
                safe and sound. You're welcome :)
            </div>
        </div>
	</section>

@stop
