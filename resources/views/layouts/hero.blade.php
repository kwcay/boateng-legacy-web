<!doctype html>
<html lang="en">
<head>
    @include('partials.head')
</head>
<body class="half-hero" id="boateng-app">
    @include('partials.css')
    @include('partials.js')

    <div class="home-banner" style="background-image:url({{ App\Utilities::bgSrc() }});">
        <div class="jumbotron">
            <div class="container text-center">
                @yield('hero')
            </div>
        </div>
    </div>

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
