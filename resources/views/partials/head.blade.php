
<meta charset="utf-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>@yield('title', trans('branding.title') .': '. trans('branding.tag_line'))</title>

@section('head')
    <base href="{{ Request::root() }}/">
    <meta name="description" content="@yield('description', trans('branding.tag_line'))">
    <meta name="topic" content="Culture, Languages">
    <meta name="keywords" content="@yield('keywords', 'dictionary, encyclopedia, bilingual, multilingual, translation')">
    <meta name="robots" content="index, follow">
    <meta name="coverage" content="Worldwide">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta property="og:title" content="@yield('title', trans('branding.title') .': '. trans('branding.tag_line'))">
    <meta property="og:desc" content="@yield('description', trans('branding.tag_line'))">
    <meta property="og:type" content="website">
    <link type="text/plain" rel="author" href="{{ Request::root() }}/humans.txt" />
    <!-- <link
        rel="search"
        type="application/opensearchdescription+xml"
        href=" route('api.os') " title="Di Nkɔmɔ Cultural Reference"> -->
@show

@include('partials.analytics')
