
<!--[if lt IE 9]> <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script> <![endif]-->
<script src="{{ App\Utilities::rev('app.js') }}" type="text/javascript"></script>
@if (Auth::check())
<script src="{{ App\Utilities::rev('user.js') }}" type="text/javascript"></script>
@endif
