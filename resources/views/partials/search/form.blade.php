
<form
    name="dictionary"
    class="search form"
    action="{{ isset($code) && $code ? route('language', [$code]) : route('search') }}">

    <div class="row">
        <div class="col-xs-12 col-lg-10 col-lg-offset-1">
            <div class="input-wrapper">
                <input
                    name="clear"
                    type="button"
                    value="&#10005;"
                    onclick="document.dictionary.q.value = ''; return false;"
                    class="remove-btn-style">

                <input
                    name="q"
                    type="text"
                    value="{{ $query or '' }}"
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
// $(document).ready(function() {
//     $(document.dictionary).dictionary({
//         langCode: "{{ $code or '' }}",
//         langName: "{{ $name or '' }}",
//         container: "{{ $container or '#results' }}"
//     });
// });

// $(document).ready(function() {
//     new DiNkomoDictionary({
//         langCode: "{{ $code or '' }}"
//     });
// });
</script>
