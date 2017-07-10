@extends('layouts.half-hero')

@section('title', $definition->mainTitle .' meaning in '. $definition->mainLanguage->name)

@section('hero')

    {{-- TODO: use languageList instead, through helper --}}
    <h1 class="definition-title">
        {{ $definition->mainTitle }}
    </h1>
    <h4>
        @if ($definition->type == 'word')
            means
            <em>
                <a href="{{ route('definition', $definition->uniqueId) }}">
                    {{ $definition->translationData->eng->practical }}
                </a>
            </em>

            in
            <em>
                <a href="{{ route('language', $definition->mainLanguage->code) }}">
                    {{ $definition->mainLanguage->name }}
                </a>
            </em>
        @else
            <em>
                <a href="{{ route('definition', $definition->uniqueId) }}">
                    {{ $definition->translationData->eng->practical }}
                </a>
            </em>
        @endif
    </h4>

@stop

@section('body')

    {{-- Admin tools --}}
    @if (Auth::check())
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 col-sm-10 col-sm-offset-1 well">
                @if ($formTemplate)
                    @include($formTemplate)
                @endif

                <input type="button" class="form-like" value="delete">
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-lg-6 col-lg-offset-3 definition-meta">

            {{-- Definition title --}}
            <div class="row">
                <div class="col-sm-4 meta-param">
                    {{ $definition->type }} :
                </div>
                <div class="col-sm-8 meta-value definition-title">
                    <a href="{{ route('definition', $definition->uniqueId) }}">
                        {{ $definition->titleString ?: $definition->mainTitle }}
                    </a>
                </div>
            </div>

            {{-- Translation --}}
            <div class="row">
                <div class="col-sm-4 meta-param">
                    translation :
                </div>
                <div class="col-sm-8 meta-value">
                    {{ $definition->translationData->eng->practical }}
                    <code>
                        {{ $definition->subType }}
                    </code>
                </div>
            </div>

            {{-- Meaning --}}
            @if ($definition->translationData->eng->meaning)
            <div class="row">
                <div class="col-sm-4 meta-param">
                    meaning :
                </div>
                <div class="col-sm-8 meta-value">
                    {{ $definition->translationData->eng->meaning }}
                </div>
            </div>
            @endif

            {{-- Literal meaning --}}
            @if ($definition->translationData->eng->literal)
            <div class="row">
                <div class="col-sm-4 meta-param">
                    literally :
                </div>
                <div class="col-sm-8 meta-value">
                    {{ $definition->translationData->eng->literal }}
                </div>
            </div>
            @endif

            {{-- Language --}}
            <div class="row">
                <div class="col-sm-4 meta-param">
                    language :
                </div>
                <div class="col-sm-8 meta-value">
                    <a href="{{ route('language', $definition->mainLanguageCode) }}">
                        {{ $definition->mainLanguage->name }}
                    </a>

                    @if ($definition->mainLanguage->altNames)
                        ({{ trim($definition->mainLanguage->altNames) }})
                    @endif
                </div>
            </div>

            {{-- Miscellaneous --}}
            <div class="row text-muted">
                <div class="col-sm-4 meta-param">
                    updated on :
                </div>
                <div class="col-sm-8 meta-value">
                    {{ date('M j, Y', strtotime($definition->updatedAt)) }}
                </div>
            </div>
            <div class="row text-muted">
                <div class="col-sm-4 meta-param">
                    contributors :
                </div>
                <div class="col-sm-8 meta-value">
                    Dora Boateng
                </div>
            </div>
            <div class="row text-muted">
                <div class="col-sm-4 meta-param">
                    id :
                </div>
                <div class="col-sm-8 meta-value">
                    {{ $definition->uniqueId }}
                </div>
            </div>
        </div>
    </div>

    @include('partials.ui.divider')

    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-lg-6 col-lg-offset-3">
            @lang('branding.pitch')
            <a href="http://goo.gl/WcthaE">Learn more</a>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-md-offset-3 col-sm-10 col-sm-offset-1">
            <div class="shaded-well" style="background-image:url(/img/bg/9ad291d387f72a04a57e2b4c8945f945-640x480.jpg);">
                <a href="{{ route('language', $definition->mainLanguageCode) }}" class="card-btn shade-50">
                    Learn more about

                    <h3>{{ $definition->mainLanguage->name }}</h3>
                </a>
            </div>
        </div>

        <!-- <div class="col-md-6">
            <div class="shaded-well" style="background-image:url(/img/bg/9ad291d387f72a04a57e2b4c8945f945-640x480.jpg);">
                <a href="/" class="card-btn shade-50">
                    Know a thing or two in <em>{{ $definition->mainLanguage->name }}</em>?
                    <br>
                    <br>

                    Suggest your own words or sayings.
                </a>
            </div>
        </div> -->
    </div>

@stop
