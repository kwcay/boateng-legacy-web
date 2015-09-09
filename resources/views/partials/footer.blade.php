<footer>
    <div class="shortcuts-wrap">
        <div class="shortcuts collapsed">
            <a
                href="{{ route('home') }}"
                class="fa fa-home has-inline-tooltip"
                data-position="top center"></a>
            <span class="ui popup">Home of Di Nkɔmɔ</span>

            <a
                href="{{ route('about') }}"
                class="fa fa-info has-inline-tooltip"
                onclick="return Dialogs.open('info');"
                data-position="top center"></a>
            <span class="ui popup">About this app</span>

            @if (Auth::guest())
                <a
                    href="{{ url('login') }}"
                    class="fa fa-unlock-alt has-inline-tooltip"
                    onclick="return Dialogs.open('login');"
                    data-position="top center"></a>
                <span class="ui popup">Login</span>
            @else
                <a
                    href="#"
                    class="fa fa-wrench has-inline-tooltip"
                    data-position="top center"></a>
                <span class="ui popup">Account Settings</span>
            @endif

            <a
                href="#"
                class="fa fa-graduation-cap has-inline-tooltip"
                onclick="alert('To do: learning games'); return false;"
                data-position="top center"></a>
            <span class="ui popup">Learn</span>

            <a
                href="#"
                class="fa fa-pencil has-inline-tooltip"
                onclick="return Dialogs.open('resource');"
                data-position="top center"></a>
            <span class="ui popup">Edit Di Nkɔmɔ</span>

            <a
                href="#"
                class="fa fa-globe has-inline-tooltip"
                onclick="return Dialogs.open('language');"
                data-position="top center"></a>
            <span class="ui popup">Find a language</span>

            @if (Auth::check())
                <a
                    href="{{ route('admin') }}"
                    class="fa fa-cogs has-inline-tooltip"
                    data-position="top center"></a>
                <span class="ui popup">Admin</span>

                <a
                    href="{{ route('auth.logout') }}"
                    class="fa fa-hand-peace-o has-inline-tooltip"
                    data-position="top center"></a>
                <span class="ui popup">Logout</span>
            @endif
        </div>
    </div>
	<div class="credits">
		<small>
            &copy; 2014 - {{ date('Y') }}
            &nbsp;&ndash;&nbsp;
			<a href="{{ route('about') }}">A Ghanaian app</a>
		</small>
	</div>
</footer>

@include('partials.dialogs')
