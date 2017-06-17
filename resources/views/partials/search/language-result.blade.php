
<div class="row">
    <div class="col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 text-left">
        {{ $rank }}.
        <a href="{{ route('language', $language->code) }}">
            {{ $language->name }}

            @if ($language->altNames)
                ({{ trim($language->altNames) }})
            @endif
        </a>
        is a language.
    </div>
</div>
