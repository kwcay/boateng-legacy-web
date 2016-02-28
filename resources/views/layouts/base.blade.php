<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <title>@yield('title', 'Di Nkomo: A Cultural Reference.')</title>

	@section('head')
        <base href="{{ Request::root() }}/">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
		<meta name="author" content="Francis Amankrah" />
		<meta name="description" content="@yield('description', 'The book of native tongues.')" />
		<meta name="keywords" content="dictionary, bilingual, multilingual, translation, twi, ewe, ga, wa, dagbani, igbo" />
		<meta name="robots" content="noindex, nofollow" />
		<meta name="csrf-token" content="{{ csrf_token() }}" />
		<meta property="og:title" content="@yield('title', 'Di Nkomo: the book of native tongues.')" />
		<meta property="og:desc" content="@yield('description', 'The book of native tongues.')" />
		<meta property="og:type" content="website" />
        <script src="{{ elixir('assets/js/dinkomo.js') }}" type="text/javascript"></script>
		<!--[if lt IE 9]> <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script> <![endif]-->
		<link rel="stylesheet" type="text/css" href="{{ elixir('assets/css/dinkomo.css') }}" />
        @include('partials.analytics')
	@show
</head>
<body>
	@yield('body')
</body>
</html>
