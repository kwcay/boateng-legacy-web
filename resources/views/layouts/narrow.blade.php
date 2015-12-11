<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <title>@yield('title', 'Di Nkomo: a Collection of Cultures.')</title>

	@section('head')
        <base href="{{ Request::root() }}/">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta name="author" content="Francis Amankrah">
		<meta name="description" content="@yield('description', 'The book of native tongues.')">
		<meta name="keywords" content="dictionary, bilingual, multilingual, translation, twi, ewe, ga, wa, dagbani, igbo">
		<meta name="robots" content="noindex, nofollow">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<meta property="og:title" content="@yield('title', 'Di Nkomo: the book of native tongues.')">
		<meta property="og:desc" content="@yield('description', 'The book of native tongues.')">
		<meta property="og:type" content="website">
        <script src="{{ elixir('assets/js/dinkomo.js') }}" type="text/javascript"></script>
		<!--[if lt IE 9]> <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script> <![endif]-->
		<link rel="stylesheet" type="text/css" href="{{ elixir('assets/css/dinkomo.css') }}">
	@show
</head>
<body>

    @include('partials.header')

	<div class="container-fluid">
        <div class="row">
            <div class="col-xs-10 col-xs-offset-1 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
    	        @yield('body')
            </div>
        </div>
	</div>

    @include('partials.footer')
    <script type="text/javascript">
        $(document).ready(function(event) {
            App.init();
        });
    </script>
</body>
</html>
