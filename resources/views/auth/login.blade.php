@extends('layouts.base')

@section('body')
	@include('partials.header')

	<section>
		<h1>Login</h1>

        <form class="form edit" name="login" method="post" action="{{ route('auth.login.post') }}">
            {!! csrf_field() !!}
            <input type="hidden" name="next" value="{{ Request::input('next', Session::pull('next')) }}">

			{{-- Email --}}
			<div class="row">
				<input type="email" name="email" placeholder="your email" required autofocus>
			</div>

			{{-- Password --}}
			<div class="row">
				<input type="password" name="password" placeholder="your password" required>
			</div>

			{{-- Submit --}}
			<div class="row center">
				<input type="submit" value="login">
			</div>

        </form>

	</section>

	@include('partials.footer')
@stop
