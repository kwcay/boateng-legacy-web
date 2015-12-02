<footer>
    <div class="shortcuts">
        <a href="{{ route('admin') }}" class="fa fa-cubes">Admin</a>
        <a href="#" class="fa fa-cog">Config</a>
        <a href="#" class="fa fa-pencil" onclick="return Dialogs.open('resource');">Res</a>
        <a href="#" class="fa fa-globe" onclick="return Dialogs.open('language');">Lang</a>
        <a href="{{ route('auth.logout') }}" class="fa fa-hand-peace-o">Logout</a>
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
