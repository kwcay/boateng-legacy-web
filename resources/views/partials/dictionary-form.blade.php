
<form name="dictionary" class="search form">
    <div class="row">
        <div class="col-xs-12 col-lg-10 col-lg-offset-1">
            <div class="input-wrapper">
                <input
                    name="clear"
                    type="button"
                    value="&#10005;"
                    class="remove-btn-style">

                <input
                    name="q"
                    type="text"
                    value="{{ Request::input('q') }}"
                    placeholder="start here"
                    autocomplete="off">

                <input
                    type="submit"
                    value="&#10163;"
                    class="remove-btn-style">
            </div>
        </div>
    </div>
</form>

<script type="text/javascript">
$(document).ready(function() {
    new DiNkomoDictionary({
        langCode: "{{ $code or '' }}"
    });
});
</script>
