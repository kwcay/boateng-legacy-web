<footer>
    <div class="shortcuts-wrap">
        <div class="shortcuts collapsed">
            <a title="Home" href="{{ route('home') }}" class="fa fa-home"></a>
            <a title="Info" href="{{ route('about') }}" class="fa fa-info"
                onclick="return App.openDialog('info');"></a>
            <a title="Login" href="{{ URL::to('login') }}" class="fa fa-unlock-alt"
                onclick="return App.openDialog('login');"></a>
            <a title="Learn" href="#" class="fa fa-graduation-cap"
                onclick="alert('To do: learning games'); return false;"></a>
            <a title="Settings" href="#" class="fa fa-wrench"
                onclick="return App.openDialog('settings');"></a>
            <a title="Suggest a new word" href="{{ route('definition.create') }}" class="fa fa-pencil"></a>
        </div>
    </div>
	<div class="credits">
		<small>
            &copy; 2014 - {{ date('Y') }}
            &nbsp;&ndash;&nbsp;
			<a href="{{ route('about') }}">A Ghanaian product</a>
		</small>
	</div>
</footer>

{{-- Info dialog --}}
<div class="dialog info">
	<div>
		<a href="#" class="close">&#10005;</a>

		<h1>What is this?</h1>
		<div class="center" style="margin-bottom: 20px;">
            This is a <em>multilingual dictionary</em> focused on <em>indigenous languages</em> from around the world.
            Or at least, that's what we're trying to make it. <a href="{{ route('about') }}">Find out more</a>.
        </div>

        <div class="center">
            There are over 7,000 languages spoken around the world today, and our web app is slowly growing to match
            that&mdash;one word at a time. <a href="{{ route('stats') }}">Check out the stats</a>.
        </div>

	</div>
</div>

{{-- Login form dialog --}}
<div class="dialog login">
	<div>
		<a href="#" class="close">&#10005;</a>

		<h1>Login</h1>
		<div class="center">
		    <br />

		    <a href="#" class="fa fa-lg fa-fw fa-linkedin" onclick="return alert('To do: LinkedIn login')"></a>
		    <a href="#" class="fa fa-lg fa-fw fa-github" onclick="return alert('To do: Github login')"></a>
		    <a href="#" class="fa fa-lg fa-fw fa-reddit" onclick="return alert('To do: Reddit login')"></a>
		    <a href="#" class="fa fa-lg fa-fw fa-twitter" onclick="return alert('To do: Twitter login')"></a>
		    <a href="#" class="fa fa-lg fa-fw fa-facebook" onclick="return alert('To do: Facebook login')"></a>
		    <a href="#" class="fa fa-lg fa-fw fa-google-plus" onclick="return alert('To do: G+ login')"></a>
		    <br /><br />

		    <em>~ or ~</em>
		    <br /><br />

		    <form class="form" action="{{ url('/auth/login') }}" method="POST">
		        <input class="single" type="email" name="email" placeholder="email" />
		        <input class="single" type="password" name="password" placeholder="password" />
		        <input type="submit" value="sign in" style="width:75px;margin:0 auto;" />
		        <input type="hidden" name="remember" value="true" />
		        {!! Form::token() !!}
		    </form>
		</div>

	</div>
</div>

{{-- Settings dialog --}}
<div class="dialog settings">
	<div>
		<a href="#" class="close">&#10005;</a>

		<h1>Settings</h1>
		<div>Target language, ...</div>

	</div>
</div>

{{-- Helper keyboard --}}
<div id="keyboard">
    <span class="move fa fa-arrows"></span>
    <span class="close fa fa-times" onclick="$('#keyboard').fadeOut(300)"></span>
    <a href="#" class="button" onclick="return App.keyboardInput(this.innerHTML)">ɛ</a>
    <a href="#" class="button" onclick="return App.keyboardInput(this.innerHTML)">ɔ</a>
    <a href="#" class="button" onclick="return App.keyboardInput(this.innerHTML)">õ</a>
    <span class="title">(helper keyboard)</span>
</div>

