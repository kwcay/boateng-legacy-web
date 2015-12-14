@extends('layouts.narrow')

@section('body')

	<h1>
	    Import Data
	    <br>

	    <small>
	        <a href="{{ route('admin.export') }}">&rarr; or click here to export</a>
	    </small>
    </h1>


	<form
        class="form edit"
        enctype="multipart/form-data"
        method="post"
        action="{{ route('admin.import.action') }}">
		{!! csrf_field() !!}

	    <input type="file" name="data">
	    <br>

	    <input type="submit" value="Import">
	</form>

@stop
