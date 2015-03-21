@extends('layouts.base')

@section('body')
	@include('layouts.header')

	<section>
		<h1>
			Suggest a new <i>definition</i><br />
			<small>
				<a href="edit?what=lang">
					&rarr; or click here to suggest a language
				</a>
			</small>
		</h1>
		<form class="form edit" method="post" action="edit">
		
			<!-- Word -->
			<div class="row">
				<input type="text" name="word" class="text-input" placeholder="e.g. k&#603;se" value="{{ $def->getWord() }}" />
				<label for="word">Word</label>
			</div>
			
			<!-- Alternate spellings -->
			<div class="row">
				<input type="text" name="alt" class="text-input" placeholder="e.g. kese, kesie" value="{{ $def->getAltSpellings() }}" />
				<label for="alt">Alternate spellings (seperated by ",")</label>
			</div>
			
			<fieldset>
				<legend>TODO: Group these in tabs for ENG, FRA, ESP, POR</legend>
				
				<!-- Translation -->
				<div class="row">
					<input type="text" name="translation[eng]" value="[def:tr:eng]" />
					<label for="translation[eng]">Translation (English)</label>
				</div>
				
				<!-- Meaning -->
				<div class="row">
					<input type="text" name="meaning[eng]" value="[def:meaning:eng]" />
					<label for="meaning[eng]">Meaning (English)</label>
				</div>
			</fieldset>
			
			<!-- Language -->
			<div class="row">
				<input type="text" name="language" class="text-input" value="{{ $def->language }}" />
				<label for="language">Languages (seperated by ",")</label>
			</div>
			
			<!-- Form actions -->
			<div class="row center">
				<input type="button" name="cancel" value="cancel" onclick="return alert('TODO: return to home page')" />
				<input type="submit" name="submit" value="save" />
			</div>
			
			{{ Form::token() }}
			<input type="hidden" name="what" value="save-definition" />
			<input type="hidden" name="def" value="" />
			<input type="hidden" name="isnew" value="1" />
		</form>
	</section>

	@include('layouts.footer')
@stop
