@extends('layouts.base')

@section('body')
	@include('layouts.header')

	<section>
		<h1>Admin Stuff</h1>

		<h2>Export</h2>
		@include('admin._export')

		<h2>Import</h2>
		@include('admin._import')

	</section>

	@include('layouts.footer')
@stop