<!doctype html>
<html lang="en">
<head>
    @include('partials.head')
</head>
<body class="half-hero" id="boateng-app">
    @include('partials.css')
    @include('partials.js')

    <br>
    <br>
    @section('errors')
        @include('partials.errors')
    @show

	<div class="container-fluid text-center">
        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2">
    	        @yield('body')
            </div>
        </div>
	</div>

    <div class="container-fluid text-center">
        <div class="row">
            <div class="col-sm-12">
                @include('partials.footer')
            </div>
        </div>
    </div>

</body>
</html>
