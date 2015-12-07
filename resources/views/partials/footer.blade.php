
<div class="row">
    <div class="col-xs-12">
        <footer>
            <div class="shortcuts">
                <a href="{{ route('home') }}" class="fa fa-book"></a>
                <a href="#" class="fa fa-pencil" onclick="return Dialogs.open('resource');"></a>
                <a href="#" class="fa fa-globe" onclick="return Dialogs.open('language');"></a>

                @if (Auth::check())
                    <a href="{{ route('admin') }}" class="fa fa-cubes"></a>
                    <a href="{{ route('auth.logout') }}" class="fa fa-hand-peace-o"></a>
                @else
                    <a href="{{ route('auth.login') }}" class="fa fa-user"></a>
                @endif
            </div>

            <a href="{{ route('about') }}" class="credits">
                &copy; 2014 - {{ date('Y') }}
                <span class="ghana no-underline"></span>
            	A <b>Ghanaian</b> app
            </a>
        </footer>
    </div>
</div>

@include('partials.dialogs')
