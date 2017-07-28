@extends('layouts.half-hero')

@section('title', $definition->titleString .' meaning in '. array_first($definition->languages)->name)

@section('hero')

    <h1 class="definition-title">
        {{ $definition->titleString }}
    </h1>
    <h4>
        @if ($definition->type == 'word')
            means
            <em>
                <a href="{{ route('definition.show', $definition->uniqueId) }}">
                    {{ \App\Utilities\DefinitionHelper::trans($definition) }}
                </a>
            </em>

            in
            <em>
                <a href="{{ route('language', array_first($definition->languages)->code) }}">
                    {{ array_first($definition->languages)->name }}
                </a>
            </em>
        @else
            <em>
                <a href="{{ route('definition.show', $definition->uniqueId) }}">
                    {{ \App\Utilities\DefinitionHelper::trans($definition) }}
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
                <a href="{{ route('definition.edit', $definition->uniqueId) }}" class="form-like">edit</a>
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
                    <a href="{{ route('definition.show', $definition->uniqueId) }}">
                        {{ $definition->titleString }}
                    </a>
                </div>
            </div>

            {{-- Translation --}}
            <div class="row">
                <div class="col-sm-4 meta-param">
                    translation :
                </div>
                <div class="col-sm-8 meta-value">
                    {{ \App\Utilities\DefinitionHelper::trans($definition) }}
                    <code>
                        {{ $definition->subType }}
                    </code>
                </div>
            </div>

            {{-- Meaning --}}
            @if (\App\Utilities\DefinitionHelper::trans($definition, 'meaning'))
            <div class="row">
                <div class="col-sm-4 meta-param">
                    meaning :
                </div>
                <div class="col-sm-8 meta-value">
                    {{ \App\Utilities\DefinitionHelper::trans($definition, 'meaning') }}
                </div>
            </div>
            @endif

            {{-- Literal meaning --}}
            @if (\App\Utilities\DefinitionHelper::trans($definition, 'literal'))
            <div class="row">
                <div class="col-sm-4 meta-param">
                    literally :
                </div>
                <div class="col-sm-8 meta-value">
                    {{ \App\Utilities\DefinitionHelper::trans($definition, 'literal') }}
                </div>
            </div>
            @endif

            {{-- Language --}}
            <div class="row">
                <div class="col-sm-4 meta-param">
                    language :
                </div>
                <div class="col-sm-8 meta-value">
                    <a href="{{ route('language', array_first($definition->languages)->code) }}">
                        {{ array_first($definition->languages)->name }}
                    </a>

                    @if (array_first($definition->languages)->altNames)
                        ({{ trim(array_first($definition->languages)->altNames) }})
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
                <a href="{{ route('language', array_first($definition->languages)->code) }}" class="card-btn shade-50">
                    Learn more about

                    <h3>{{ array_first($definition->languages)->name }}</h3>
                </a>
            </div>
        </div>

        <!-- <div class="col-md-6">
            <div class="shaded-well" style="background-image:url(/img/bg/9ad291d387f72a04a57e2b4c8945f945-640x480.jpg);">
                <a href="/" class="card-btn shade-50">
                    Know a thing or two in <em>{{ array_first($definition->languages)->name }}</em>?
                    <br>
                    <br>

                    Suggest your own words or sayings.
                </a>
            </div>
        </div> -->
    </div>

@stop
