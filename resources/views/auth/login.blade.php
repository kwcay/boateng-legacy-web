@extends('layouts.narrow')

@section('body')

	<h1>
	    Sign in with your social media account
	</h1>

    <div class="row">

        <div class="col-sm-12 text-center">

            {{-- Personas --}}
            <a href="javascript:;"
                class="button"
                title="Firefox Personas"
                data-toggle="tooltip">
                <span class="fa fa-fw fa-firefox"></span>
            </a>

            {{-- Github --}}
            <a href="{{ route('auth.oauth', 'github') }}"
                class="button"
                title="Github"
                data-toggle="tooltip">
                <span class="fa fa-fw fa-github"></span>
            </a>

            {{-- Google+ --}}
            <a href="{{ route('auth.oauth', 'google') }}"
                class="button"
                title="Google Plus"
                data-toggle="tooltip">
                <span class="fa fa-fw fa-google-plus"></span>
            </a>

            {{-- Twitter --}}
            <a href="{{ route('auth.oauth', 'twitter') }}"
                class="button"
                title="Twitter"
                data-toggle="tooltip">
                <span class="fa fa-fw fa-twitter"></span>
            </a>

            {{-- Facebook --}}
            <a href="{{ route('auth.oauth', 'facebook') }}"
                class="button"
                title="Facebook"
                data-toggle="tooltip">
                <span class="fa fa-fw fa-facebook"></span>
            </a>

        </div>
    </div>
    <br>
    <br>

    <h1>
        Or use your @lang('branding.title') credentials
    </h1>

    <form class="form edit" name="login" method="post" action="{{ route('auth.login.post') }}">
        {!! csrf_field() !!}
        <input type="hidden" name="next" value="{{ Request::input('next', Session::pull('next')) }}">

		{{-- Email --}}
		<div class="row">
			<div class="col-xs-12 col-md-8 col-md-offset-2">
			    <input type="email" name="email" placeholder="your email" required autofocus>
			</div>
		</div>

		{{-- Password --}}
		<div class="row">
			<div class="col-xs-12 col-md-8 col-md-offset-2">
			    <input type="password" name="password" placeholder="your password" required>
			</div>
		</div>

		{{-- Submit --}}
		<div class="row center">
			<div class="col-xs-12">
			    <input type="submit" value="login">
			</div>
		</div>
    </form>

@stop
