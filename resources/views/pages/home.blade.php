@extends('layouts.base')

@section('body')
	@include('layouts.header')

    <div class="title">
        Di Nkɔmɔ
        <br />
        <small>The Book of Native Tongues.</small>
    </div>

	<section class="form search">

		<form name="search">
			<div class="container">
				<input class="remove-btn-style" name="clear" type="button" value="&#10005;" />
				<input name="q" type="text" placeholder="start here" value="{{ Input::get('q') }}" autocomplete="off" />
				<input class="remove-btn-style" name="submit" type="submit" value="&#10163;" />
				<div class="clr"></div>
			</div>
		</form>

		<div id="results">
		    <div class="center">Use this <em>&#10548;</em> to lookup words<br />in another language.</div>
		</div>

	</section>

	<script type="text/javascript">
	Forms.setupDefinitionLookup('search');
    
    $(document).ready(function() { $(document.search).submit(); });
    
	</script>

	@include('layouts.footer')
@stop
