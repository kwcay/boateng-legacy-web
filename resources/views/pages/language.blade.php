@extends('layouts.half-hero')

{{-- Page meta --}}
@section('title', trans('language.title', ['language' => $lang->name]))
@section('description', trans('language.description', ['language' => $lang->name]))
@section('keywords', implode(',', ['dictionary', 'learn', $lang->name, $lang->code, 'free']))

@section('hero')

    <h1 class="definition-title">
        {{ $lang->name }}
    </h1>

    <h4>
        @lang('language.lookup-definitions')
    </h4>

    {{-- Only include search form if language has definitions to show --}}
    @if ($lang->randomDefinition)
        @include('partials.search.form', ['code' => $lang->code, 'name' => $lang->name])
    @endif

@stop

@section('body')

    @include('partials.search.results')

    {{-- Edit form --}}
    @if (Auth::check())
        <div class="row">
            <div class="">
                @include('forms.language.modal')
            </div>
        </div>
    @endif

    {{-- Quick info --}}
    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-lg-6 col-lg-offset-3 definition-meta">

            {{-- Language name --}}
            <div class="row">
                <div class="col-sm-4 meta-param">
                    language :
                </div>
                <div class="col-sm-8 meta-value definition-title">
                    <a href="{{ route('language', $lang->code) }}">
                        {{ $lang->name }}
                    </a>
                </div>
            </div>

            {{-- Alternate names --}}
            @if ($lang->altNames)
            <div class="row">
                <div class="col-sm-4 meta-param">
                    also referred to as :
                </div>
                <div class="col-sm-8 meta-value">
                    {{ $lang->altNames }}
                </div>
            </div>
            @endif

            @if (false)
            <div class="row">
                <div class="col-sm-4 meta-param">
                    spoken in :
                </div>
                <div class="col-sm-8 meta-value">
                    {{ $lang->countryString }}
                </div>
            </div>
            @endif

            @if (isset($lang->parentName) && $lang->parentName)
            <div class="row">
                <div class="col-sm-4 meta-param">
                    child language of :
                </div>
                <div class="col-sm-8 meta-value">
                    <a href="{{ route('language', $lang->parentCode) }}">
                        {{ $lang->parentName }}
                    </a>
                </div>
            </div>
            @endif

            {{-- Miscellaneous --}}
            <div class="row text-muted">
                <div class="col-sm-4 meta-param">
                    updated on :
                </div>
                <div class="col-sm-8 meta-value">
                    {{ date('M j, Y', strtotime($lang->updatedAt)) }}
                </div>
            </div>
            @if (isset($lang->definitionCount) && $lang->definitionCount)
            <div class="row text-muted">
                <div class="col-sm-4 meta-param">
                    definition count :
                </div>
                <div class="col-sm-8 meta-value">
                    {{ number_format($lang->definitionCount) }}
                </div>
            </div>
            @endif
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
                    {{ $lang->uniqueId }}
                </div>
            </div>
        </div>
    </div>

    @include('partials.ui.divider')

    @if (isset($lang->randomDefinition) && $lang->randomDefinition)
    <div class="row">
        <div class="col-md-6 col-md-offset-3 col-sm-10 col-sm-offset-1">
            <div class="shaded-well" style="background-image:url(/img/bg/9ad291d387f72a04a57e2b4c8945f945-640x480.jpg);">
                <a href="{{ route('definition.show', $lang->randomDefinition->uniqueId) }}" class="card-btn shade-50">
                    Discover what
                    <br>

                    <em style="font-style:normal;font-size:1.2em;">
                        {{ $lang->randomDefinition->mainTitle }}
                    </em>
                    <br>

                    means in {{ $lang->name }}
                </a>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <hr>

        <div class="col-md-6">
            @lang('branding.pitch')
        </div>

        <div class="col-md-6">
            <a href="http://eepurl.com/cKEMKP" target="_blank">Get notified</a> as we add new
            content and features. If you're interested in sharing your feedback or ideas,
            don't hesitate to <a href="http://goo.gl/WcthaE">take our survey</a> or
            <a href="mailto:frank@doraboateng.com">shoot us an email</a>.
        </div>

        <hr>
    </div>

@stop
