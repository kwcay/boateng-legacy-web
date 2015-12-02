@extends('layouts.base')

@section('body')
	@include('partials.header')

	<div class="col-xs-10 col-xs-offset-1 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
		<h1>Login</h1>

        <form class="form edit" name="login" method="post" action="{{ route('auth.login.post') }}">
            {!! csrf_field() !!}
            <input type="hidden" name="next" value="{{ Request::input('next', Session::pull('next')) }}">

			{{-- Email --}}
			<div class="row">
				<div class="col-xs-12">
				    <input type="email" name="email" placeholder="your email" required autofocus>
				</div>
			</div>

			{{-- Password --}}
			<div class="row">
				<div class="col-xs-12">
				    <input type="password" name="password" placeholder="your password" required>
				</div>
			</div>

			{{-- Submit --}}
			<div class="row text-center">
				<div class="col-xs-12">
				    <input type="submit" value="login">
				</div>
			</div>
        </form>
	</div>

	@include('partials.footer')
@stop
