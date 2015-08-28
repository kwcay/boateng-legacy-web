@extends('layouts.base')

@section('body')
	@include('layouts.header')

	<section>
		<h1>
			Suggest a new <i>language</i><br />
			<small>
				<a href="edit">
					&rarr; or click here to suggest a new definition
				</a>
			</small>
		</h1>
		<form class="form edit" method="post">
		
			<!-- Title -->
			<div class="row">
				<input type="text" name="name" class="text-input" placeholder="e.g. ..." value="{{ $lang->getName() }}" />
				<label for="name">Name</label>
			</div>
			
			<!-- Alternate spellings -->
			<div class="row">
				<input type="text" name="alt" class="text-input" placeholder="e.g. ..." value="{{ $lang->getAltSpellings() }}" />
				<label for="alt">Alternate spellings (seperated by ",")</label>
			</div>
			
			<!-- ISO-639-3 -->
			<div class="row">
				<input type="text" name="code" placeholder="e.g. ..." value="{{ $lang->code }}" />
				<label for="code"><a href="http://en.wikipedia.org/wiki/ISO_639-3" target="_blank">ISO-639-3</a> language code</label>
			</div>
			
			<!-- Countries -->
			<div class="row">
				<input type="text" name="countries" placeholder="e.g. ..." value="{{ $lang->countries }}" />
				<label for="countries">Countries in which language is spoken</label>
			</div>
			
			<!-- Form actions -->
			<div class="row center">
				<input type="button" name="cancel" value="cancel" />
				<input type="submit" name="submit" value="save" />
			</div>
			
			{{ Form::token() }}
			<input type="hidden" name="what" value="save-language" />
			<input type="hidden" name="isnew" value="1" />
		</form>
	</section>

	@include('layouts.footer')
@stop
