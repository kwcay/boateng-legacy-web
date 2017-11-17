
<link rel="stylesheet" type="text/css" href="{{ App\Utilities::rev('app.css') }}">
@if (Auth::check())
<link rel="stylesheet" type="text/css" href="{{ App\Utilities::rev('user.css') }}">
@endif
