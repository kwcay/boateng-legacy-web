@extends('layouts.base')

@section('body')
	@include('layouts.header')

	<section>
        <h1>Dev</h1>
        
        @if ($langs = Language::all())
            <h2>Languages <small>({{ count($langs) }} total)</small></h2>
            @foreach ($langs as $lang)
                <a href="{{ $lang->getEditUri() }}" class="fa fa-pencil"></a>
                <a href="{{ $lang->getUri() }}">{{ $lang->getName() }}</a>
                <br />
            @endforeach
        @endif
        
        @if ($defs = Definition::all())
            <h2>Definitions <small>({{ count($defs) }} total)</small></h2>
            @foreach ($defs as $def)
                <a href="{{ $def->getEditUri() }}" class="fa fa-pencil"></a>
                <a href="{{ $def->getUri() }}">{{ $def->getWord() }}</a>
                <br />
            @endforeach
        @endif
	</section>

	@include('layouts.footer')
@stop
