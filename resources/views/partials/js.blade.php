
<!--[if lt IE 9]> <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script> <![endif]-->
<script src="{{ App\Utilities\Asset::for('app.js') }}" type="text/javascript"></script>
@if (Auth::check())
<script src="{{ App\Utilities\Asset::for('user.js') }}" type="text/javascript"></script>
@endif
