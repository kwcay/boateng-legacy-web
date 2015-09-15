@extends('layouts.base')

@section('body')
	@include('partials.header')

	<section>
		<h1>Admin Stuff</h1>

        <h2>Notifications</h2>
        ...

        <h2>Import/Export</h2>
		<ul>
		    <li>
		        <a href="{{ route('admin.import') }}">Import data.</a>
		    </li>
		    <li>
		        <a href="{{ route('admin.export') }}">Export data.</a>
		    </li>
		</ul>

	</section>

	@include('partials.footer')
@stop
