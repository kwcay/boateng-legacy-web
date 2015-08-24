@extends('layouts.base')

@section('title', $query .' is a word in '. $lang->name .' - Di Nkomo: the book of native tongues.')

@section('body')
	@include('layouts.header')

	<section>
		<h1>{{ $query }}</h1>

        <div class="center pad-bottom">
            Is a word in <em><a href="{{ $lang->getUri() }}">{{ $lang->name }}</a></em> that could translate to:
        </div>

        {{-- List of matching definitions --}}
        <div class="definitions">
            @foreach ($definitions as $def)
                <div class="definition">
                    @if ($def->id != $definitions[0]->id)
                        <span class="or">or</span>
                    @endif
                    <h3>
                        <span class="edit-res">
                            <a href="{{ $def->getEditUri() }}" class="fa fa-pencil"></a>
                        </span>
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

	</section>

	@include('layouts.footer')
@stop
