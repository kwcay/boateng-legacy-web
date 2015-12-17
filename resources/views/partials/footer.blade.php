
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
            <footer>
                <div class="shortcuts">
                    <a href="{{ route('home') }}" class="fa fa-book fa-fw"></a>
                    <!-- <a href="#" class="fa fa-plus fa-fw" onclick="return Dialogs.open('resource');"></a> -->

                    @if (Auth::check())
                        <a href="{{ route('admin') }}" class="fa fa-cubes fa-fw"></a>
                        <a href="{{ route('auth.logout') }}" class="fa fa-hand-peace-o fa-fw"></a>
                    @else
                        <a href="{{ route('auth.login') }}" class="fa fa-user fa-fw"></a>
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
</div>

@include('partials.dialogs')
