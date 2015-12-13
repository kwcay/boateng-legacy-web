
<form name="search" class="search form">
    <div class="row">
        <div class="col-xs-12">
            <div class="input-wrapper">
                <input class="remove-btn-style" name="clear" type="button" value="&#10005;">
                <input name="q" type="text" placeholder="start here" value="{{ Input::get('q') }}"
                    autocomplete="off">
                <input class="remove-btn-style" type="submit" value="&#10163;">
            </div>
        </div>
    </div>
</form>

<div id="results">
    <div class="center">
        Use this <em>&#10548;</em> to lookup words<br>
        in {{ $name or 'another language' }}.
    </div>
</div>

<script type="text/javascript">
Forms.setupDefinitionLookup('search', {
    langCode: <?= isset($code) ? '"'. $code .'"' : 'false' ?>,
    langName: "{{ $name or 'another language' }}"
});
</script>
