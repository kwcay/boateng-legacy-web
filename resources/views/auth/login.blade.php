@extends('layouts.base')

@section('body')
	@include('partials.header')

	<section>
		<h1>Login</h1>

        <form class="form edit" name="login" method="post" action="{{ route('auth.login.post') }}">
            {!! csrf_field() !!}

			{{-- Email --}}
			<div class="row">
				<input type="email" name="email" placeholder="your email" required autofocus />
				<label for="email">Email</label>
			</div>

			{{-- Password --}}
			<div class="row">
				<input type="password" name="password" placeholder="your password" required />
				<label for="password">Password</label>
			</div>

			{{-- Submit --}}
			<div class="row center">
				<input type="submit" value="login" />
			</div>

        </form>

	</section>

	@include('partials.footer')
@stop
