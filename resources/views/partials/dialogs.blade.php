
{{-- Info dialog --}}
<div class="dialog info">
	<div>
		<a href="#" class="close">&#10005;</a>

		<h1>What is this?</h1>
		<div class="center" style="margin-bottom: 20px;">
            This is a <em>multilingual dictionary</em> focused on <em>indigenous languages</em>
            from around the world. Or, that's hopefully what it'll become some day.
            <a href="{{ route('about') }}">Find out more</a>.
        </div>

        <div class="center">
            There are over 7,000 languages spoken around the world today, and this web app is
            slowly growing to match that&mdash;one word at a time.
            <a href="{{ route('stats') }}">Check out the stats</a>.
        </div>

	</div>
</div>

{{-- Login form dialog --}}
@if (Auth::guest())
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

    		    <form class="form" action="" method="POST" onsubmit="alert('TODO'); return false;">
    		        <input class="single" type="email" name="email" placeholder="email" />
    		        <input class="single" type="password" name="password" placeholder="password" />
    		        <input type="submit" value="sign in" style="width:75px;margin:0 auto;" />
    		        <input type="hidden" name="remember" value="true" />
    		        {!! Form::token() !!}
    		    </form>
    		</div>

    	</div>
    </div>
@endif

{{-- Settings dialog (not being used) --}}
<div class="dialog settings">
	<div>
		<a href="#" class="close">&#10005;</a>

		<h1>Settings</h1>
		<div>Target language, ...</div>

	</div>
</div>

{{-- Language dialog --}}
<div class="dialog language">
	<div>
		<a href="#" class="close">&#10005;</a>

		<h1>Find a language</h1>
        <div class="center">
            <form name="languageDialogForm" class="form" onsubmit="return false;">

                <div class="ui lang-search languge-dialog-search">
                    <input name="language" type="text" class="prompt center" placeholder="e.g. twi" />
                    <div class="results"></div>
                </div>

            </form>
        </div>

	</div>
</div>

{{-- Add resource dialog --}}
<div class="dialog resource">
	<div>
		<a href="#" class="close">&#10005;</a>

		<h1>Edit Di Nkɔmɔ</h1>
		<div class="center">

            <br />
            <form class="form" name="dialogResourceForm" onsubmit="return Dialogs.addResource();">
                <input type="hidden" name="lang" value="" />

                Suggest a new
                <select class="" name="">
                    @foreach (\App\Models\Definition::types() as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>

                in

                <div class="ui add-resource-input">
                    <input type="text" name="language" class="prompt center" placeholder="your language" />
                    <div class="results"></div>
                </div>

                <input type="submit" value="go" style="width: 50px; margin: 0 auto; text-align: center;">
            </form>

            <script type="text/javascript">
                Dialogs.setupAddResourceForm('.dialog.resource .ui.add-resource-input');
            </script>

		    <em>~ or ~</em>
		    <br /><br />

		    <a href="{{ route('language.walkthrough') }}">
		        click here to suggest a new language.
		    </a>
		</div>

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
