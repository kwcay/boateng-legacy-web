
<div class="form search">
    <form name="search">
        <div class="ui container">
            <input name="clear" class="remove-btn-style" type="button" value="&#10005;" />
            <input name="q" type="text" placeholder="start here" value="{{ Input::get('q') }}" autocomplete="off" />
            <input class="remove-btn-style" type="submit" value="&#10163;" />
            <div class="clr"></div>
        </div>
    </form>

    <div id="results">
        <div class="center">Use this <em>&#10548;</em> to lookup words<br />in another language.</div>
    </div>
</div>
