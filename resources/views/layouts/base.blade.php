<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <title>@yield('title', 'Di Nkomo: the book of native tongues.')</title>
    <!-- v0.2 -->

	@section('head')
        <base href="{{ Request::root() }}/">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
		<meta name="author" content="Francis Amankrah" />
		<meta name="description" content="The book of Native tongues." />
		<meta name="keywords" content="dictionary, bilingual, multilingual, translation, twi, ewe, ga, wa, dagbani, igbo" />
		<meta name="robots" content="noindex, nofollow" />
		<meta name="csrf-token" content="{{ csrf_token() }}" />
		<meta property="og:title" content="Di Nkomo: the book of Native tongues." />
		<meta property="og:desc" content="The dictionary of Native tongues." />
		<meta property="og:type" content="website" />
		<script src="{{ asset('assets/jquery-2.1.4.min.js') }}" type="text/javascript"></script>
		<script src="{{ elixir('assets/scripts.js') }}" type="text/javascript"></script>
		<!--[if lt IE 9]> <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script> <![endif]-->
		<link rel="stylesheet" type="text/css" href="assets/fonts/sinanova.css" />
		<link rel="stylesheet" type="text/css" href="{{ elixir('assets/styles.css') }}" />
	@show
</head>
<body>
	@yield('body')
</body>
</html>
