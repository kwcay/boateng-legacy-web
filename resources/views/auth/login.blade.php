@extends('layouts.plain')

@section('body')

	<h4>
	    Sign in with your account
	</h4>

    <div class="row">

        <div class="col-sm-12 text-center">

            {{-- Twitter --}}
            <a href="{{ route('oauth', 'twitter') }}"
                class="button"
                title="Twitter"
                onclick="return notImplemented(this)">
                <span class="fa fa-fw fa-twitter"></span>
            </a>

            {{-- Facebook --}}
            <a href="{{ route('oauth', 'facebook') }}"
                class="button"
                title="Facebook"
                onclick="return notImplemented(this)">
                <span class="fa fa-fw fa-facebook"></span>
            </a>

            {{-- Reddit --}}
            <a href="{{ route('oauth', 'reddit') }}"
                class="button"
                title="Reddit"
                onclick="return notImplemented(this)">
                <span class="fa fa-fw fa-reddit"></span>
            </a>

            {{-- Google+ --}}
            <a href="{{ route('oauth', 'google') }}"
                class="button"
                title="Google+"
                onclick="return notImplemented(this)">
                <span class="fa fa-fw fa-google-plus"></span>
            </a>

        </div>
    </div>
    <hr>

    <h4>
        Or use your @lang('branding.title') credentials
    </h4>

    <form class="form edit" name="login" method="post" action="{{ route('login.post') }}">
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

    <script type="text/javascript">
        function notImplemented(context) {
            alert(context.title + " login coming soon !");

            return false;
        }
    </script>

@stop
