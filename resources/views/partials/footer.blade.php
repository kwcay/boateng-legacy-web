<footer>
    <div class="shortcuts">
        <a href="{{ route('admin') }}" class="fa fa-cubes"></a>
        <a href="#" class="fa fa-cog"></a>
        <a href="#" class="fa fa-pencil" onclick="return Dialogs.open('resource');"></a>
        <a href="#" class="fa fa-globe" onclick="return Dialogs.open('language');"></a>
        <a href="{{ route('auth.logout') }}" class="fa fa-hand-peace-o"></a>
    </div>
	<div class="credits">
		<small>
            &copy; 2014 - {{ date('Y') }}
            &nbsp;&ndash;&nbsp;
			A Ghanaian app
		</small>
	</div>
</footer>

@include('partials.dialogs')
