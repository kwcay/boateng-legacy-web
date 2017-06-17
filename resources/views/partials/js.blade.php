
<script src="{{ App\Utilities::rev('app.js') }}" type="text/javascript"></script>
@if (Auth::check())
<script src="{{ App\Utilities::rev('user.js') }}" type="text/javascript"></script>
@endif
