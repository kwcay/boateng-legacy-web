@extends('layouts.hero')

@section('head')
    @parent

    <link rel="canonical" href="{{ route('home') }}">
@endsection

@section('hero')

    <h1 class="hero-title">Dora Boateng</h1>
    <!-- <h4 class="hero-subtitle">
        Lookup <a href="/?q=%23proverbs">proverbs</a>, <a href="/?q=%23stories">stories</a>, and other cultural gems.
    </h4> -->

    @include('partials.search.form')

    <div class="hero-text">
        @lang('branding.pitch')

        <h3>
            {{--<small>Let us know what you think.</small>--}}
            {{--<a href="http://goo.gl/WcthaE">Take our survey</a>--}}
            <a href="{{ route('language.learn') }}">Learn an African language</a>
        </h3>
    </div>

@stop

@section('body')

    {{-- Admin tools --}}
    @auth
        <div class="row">
            <div class="col-lg-6 col-lg-offset-3 col-sm-10 col-sm-offset-1 well">
                Temporary shortcuts:
                <a href="{{ route('language.create') }}">new language</a>
            </div>
        </div>
    @endauth

    <div class="row hidden-md hidden-lg">
        <div class="col-md-10 col-md-offset-1 col-lg-6 col-lg-offset-3">
            @lang('branding.pitch')
            <hr>
        </div>
    </div>

    @include('partials.language.weekly')

    <script type="application/ld+json">
    {
      "@context": "http://schema.org",
      "@type": "WebSite",
      "url": "https://www.doraboateng.com/",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "{{ route('search') }}?q={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
    </script>

@stop
