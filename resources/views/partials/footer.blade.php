
<footer>

    <div class="footer-row">
        <a href="{{ route('home') }}">
            <i class="fa fa-home"></i> home
        </a> |

        @if (Auth::check())
            <a href="#" onclick="alert('In development... Bear with us !');return false;">
                <i class="fa fa-address-card"></i>
                <span class="hidden-xs">
                    account
                </span>
            </a> |
            <a href="{{ route('logout') }}">
                <i class="fa fa-window-close"></i>
                <span class="hidden-xs">
                    sign out
                </span>
            </a> |
        @else
            <a href="{{ route('login') }}">
                <i class="fa fa-user-o"></i>
                <span class="hidden-xs">
                    sign in
                </span>
            </a> |
        @endif

        <a href="https://twitter.com/DoraBoatengApp" target="_blank">
            <i class="fa fa-twitter"></i>
            <span class="hidden-xs">
                Twitter
            </span>
        </a> |
        <a href="https://www.instagram.com/DoraBoatengApp" target="_blank">
            <i class="fa fa-instagram"></i>
            <span class="hidden-xs">
                Instagram
            </span>
        </a> |
        <a href="http://eepurl.com/cKEMKP" target="_blank">
            <i class="fa fa-envelope-o"></i>
            <span class="hidden-xs">
                Subscribe
            </span>
        </a>
    </div>

    <div class="footer-row">
        <a href="{{ route('definition.random') }}">
            show me something new
        </a> |
        <a href="http://goo.gl/WcthaE">
            take our survey
        </a>
    </div>

    <br>
    <div class="footer-row">
        &copy; {{ date('Y') }} @lang('branding.title')
    </div>

</footer>
