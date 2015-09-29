@extends('layouts.base')

@section('title', $query .' is a word in '. $lang->name .' - Di Nkomo: the book of native tongues.')

@section('body')
	@include('partials.header')

	<section>
		<h1>{{ $query }}</h1>

        <div class="center pad-bottom">
            Is a word in <em><a href="{{ $lang->uri }}">{{ $lang->name }}</a></em> that translates to:
        </div>

        {{-- List of matching definitions --}}
        <div class="definitions">
            @foreach ($definitions as $def)
                <div class="definition">
                    @if ($def->id != $definitions[0]->id)
                        <span class="or">or</span>
                    @endif

                    <h3>
                        @if (Auth::check())
                        <span class="edit-res">
                            <a href="{{ $def->editUri }}" class="fa fa-pencil"></a>
                        </span>
                        @endif
                        
                        &ldquo; {{ $def->getTranslation('en') }} &rdquo;
                    </h3>
                    @if ($def->hasMeaning('en'))
                        {{ $def->getMeaning('en') }}.
                    @endif
                    @if (strlen($def->altTitles))
                        Alternatively: {{ $def->altTitles }}
                    @endif
                </div>
            @endforeach
        </div>

		<br />
		<br />
		<div class="">
			Suggest a new {{ $lang->name }}
			<a href="{{ route('definition.create.word', ['lang' => $lang->code]) }}">word</a>
			or <a href="{{ route('definition.create.phrase', ['lang' => $lang->code]) }}">saying</a>.
		</div>

	</section>

	@include('partials.footer')
@stop
