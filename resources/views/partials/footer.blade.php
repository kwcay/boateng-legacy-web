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
                onclick="return App.openDialog('info');"
                data-position="top center"></a>
            <span class="ui popup">About this app</span>

            <a
                href="{{ url('login') }}"
                class="fa fa-unlock-alt has-inline-tooltip"
                onclick="return App.openDialog('login');"
                data-position="top center"></a>
            <span class="ui popup">Login</span>

            <a
                href="#"
                class="fa fa-graduation-cap has-inline-tooltip"
                onclick="alert('To do: learning games'); return false;"
                data-position="top center"></a>
            <span class="ui popup">Learn</span>

            <a
                href="#"
                class="fa fa-wrench has-inline-tooltip"
                onclick="return App.openDialog('settings');"
                data-position="top center"></a>
            <span class="ui popup">Settings</span>

            <a
                href="#"
                class="fa fa-pencil has-inline-tooltip"
                onclick="return App.openDialog('resource');"
                data-position="top center"></a>
            <span class="ui popup">Edit Di Nkɔmɔ</span>
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

		<h1>Login [todo]</h1>
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

{{-- Settings dialog --}}
<div class="dialog settings">
	<div>
		<a href="#" class="close">&#10005;</a>

		<h1>Settings</h1>
		<div>Target language, ...</div>

	</div>
</div>

{{-- Add resource dialog --}}
<div class="dialog resource">
	<div>
		<a href="#" class="close">&#10005;</a>

		<h1>Contribute to Di Nkɔmɔ</h1>
		<div class="center">

            <br />
            <form class="form" name="dialogDefinitionForm" onsubmit="return Dialogs.showDefinitionForm(this)">

                Add a

                <div class="types">
                    <a href="#" class="selected" onclick="Dialogs.toggleType(this)" data-type="word">Word</a>
                    <a href="#" onclick="Dialogs.toggleType(this)" data-type="phrase">Phrase</a>
                    <a href="#" onclick="Dialogs.toggleType(this)" data-type="poem">Poem</a>
                    <a href="#" onclick="Dialogs.toggleType(this)" data-type="story">Story</a>
                </div>

                in

                <input type="text" name="language" class="center" placeholder="your language" required />
                <!-- <div class="ui dialog-resource-lang">
                    <input type="text" name="language" class="prompt center" placeholder="your language" />
                    <div class="results"></div>
                </div> -->
                <script type="text/javascript">
                    Forms.setupLangSearch(document.dialogDefinitionForm.language, [], 20);
                    // $('.ui.dialog-resource-lang').search({
                    //     apiSettings: {
                    //         url: 'language/search/{query}?semantic=1'
                    //     },
                    //     searchFields: ['name', 'alt_names'],
                    //     searchDelay: 500,
                    //     searchFullText: false,
                    //     onSelect: function(result, response) {
                    //         Dialogs.definition.langCode = result.code;
                    //     }
                    // });
                </script>

            </form>
            <br />

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
