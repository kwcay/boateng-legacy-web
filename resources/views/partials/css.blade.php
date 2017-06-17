
<!--[if lt IE 9]> <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script> <![endif]-->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Crimson+Text:100,400,700">
<link rel="stylesheet" type="text/css" href="{{ App\Utilities::rev('app.css') }}">
@if (Auth::check())
<link rel="stylesheet" type="text/css" href="{{ App\Utilities::rev('user.css') }}">
@endif
