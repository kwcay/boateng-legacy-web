<footer>
    <div class="shortcuts-wrap">
        <div class="shortcuts collapsed">
            <a
                href="{{ route('home') }}"
                class="home icon has-inline-tooltip"
                data-position="top center"></a>
            <span class="ui popup">Home of Di Nkɔmɔ</span>

            <a
                href="{{ route('about') }}"
                class="info icon has-inline-tooltip"
                onclick="return Dialogs.open('info');"
                data-position="top center"></a>
            <span class="ui popup">About this app</span>

            @if (Auth::guest())
                <a
                    href="{{ url('login') }}"
                    class="unlock-alt icon has-inline-tooltip"
                    onclick="return Dialogs.open('login');"
                    data-position="top center"></a>
                <span class="ui popup">Login</span>
            @else
                <a
                    href="#"
                    class="wrench icon has-inline-tooltip"
                    data-position="top center"></a>
                <span class="ui popup">Account Settings</span>
            @endif

            <a
                href="#"
                class="graduation-cap icon has-inline-tooltip"
                onclick="alert('To do: learning games'); return false;"
                data-position="top center"></a>
            <span class="ui popup">Learn</span>

            <a
                href="#"
                class="pencil icon has-inline-tooltip"
                onclick="return Dialogs.open('resource');"
                data-position="top center"></a>
            <span class="ui popup">Edit Di Nkɔmɔ</span>

            <a
                href="#"
                class="globe icon has-inline-tooltip"
                onclick="return Dialogs.open('language');"
                data-position="top center"></a>
            <span class="ui popup">Find a language</span>

            @if (Auth::check())
                <a
                    href="{{ route('admin') }}"
                    class="cogs icon has-inline-tooltip"
                    data-position="top center"></a>
                <span class="ui popup">Admin</span>

                <a
                    href="{{ route('auth.logout') }}"
                    class="hand-peace-o icon has-inline-tooltip"
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
