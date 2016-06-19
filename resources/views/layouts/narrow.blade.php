<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <title>@yield('title', 'Di Nkɔmɔ: '. trans('branding.tag_line'))</title>

	@section('head')
        <base href="{{ Request::root() }}/">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta name="author" content="Francis Amankrah">
		<meta name="description" content="@yield('description', 'A reference of cultures past and present.')">
		<meta name="keywords" content="@yield('keywords', 'dictionary, bilingual, multilingual, translation')">
		<meta name="robots" content="index, follow">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<meta property="og:title" content="@yield('title', 'Di Nkɔmɔ: '. trans('branding.tag_line'))">
		<meta property="og:desc" content="@yield('description', trans('branding.tag_line'))">
		<meta property="og:type" content="website">
        <link
            rel="search"
            type="application/opensearchdescription+xml"
            href="{{ route('api.os') }}" title="Di Nkɔmɔ Cultural Reference">
	@show
</head>
<body>

    <script src="{{ App\Utilities::rev('all.js') }}" type="text/javascript"></script>
    <!--[if lt IE 9]> <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script> <![endif]-->
    <link rel="stylesheet" type="text/css" href="{{ App\Utilities::rev('all.css') }}">
    @include('partials.analytics')

    @include('partials.header')

	<div class="container-fluid">
        <div class="row">
            <div class="col-xs-10 col-xs-offset-1 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3">
    	        @yield('body')
            </div>
        </div>
	</div>

    @include('partials.footer')
</body>
</html>
