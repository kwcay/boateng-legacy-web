
{{-- Add resource dialog --}}
<div class="dialog resource">
	<div>
		<a href="#" class="close">&#10005;</a>

		<h1>Edit Di Nkɔmɔ</h1>
		<div class="center">

            <br>
            <form name="addResourceDialogForm" class="form" onsubmit="return Dialogs.addResource();">
                <input type="hidden" name="lang" value="" />

                Suggest a new
                <select name="type">
                    @foreach (\App\Models\Definition::types() as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>

                in

                <div class="semantic-search">
                    <input type="text" name="language" class="prompt center" placeholder="your language">
                    <div class="results"></div>

                    <input type="submit" name="submit" value="&#10163;">
                </div>
            </form>

            <script type="text/javascript">
                Dialogs.setupAddResourceForm('.dialog.resource .semantic-search');
            </script>

            <br>
		    <em>~ or ~</em>
		    <br><br>

		    <a href="{{ route('admin.language.create') }}">
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
    <a href="#" class="button" onclick="return App.keyboardInput(this.innerHTML)">ŋ</a>
    <a href="#" class="button" onclick="return App.keyboardInput(this.innerHTML)">ɔ</a>
    <span class="title">(helper keyboard)</span>
</div>
