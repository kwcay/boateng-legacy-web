
<h3>
    @if (Auth::check())
    <span class="edit-res">
        <a href="{{ $def->editUri }}" class="fa fa-pencil"></a>
    </span>
    @endif

    &ldquo; {{ $def->getPracticalTranslation('eng') }} &rdquo;

    {{-- Sub type --}}
    <small>
        <code>{{ $def->subType }}</code>
    </small>
</h3>

{{-- Append meaning --}}
@if ($def->hasMeaning('eng'))
    {{ $def->getMeaning('eng') }}.
@endif

{{-- Append literal meaning --}}
@if ($def->hasLiteralTranslation('eng'))
    <i>Literally</i>: {{ $def->getLiteralTranslation('eng') }}.
@endif

{{-- Append alternative spellings --}}
@if (strlen($def->altTitles))
    <i>Alternatively</i>: {{ $def->altTitles }}.
@endif

{{-- Add language information --}}
@if (count($def->languages) > 1)
    <i>Also a word in: </i>
    @foreach ($def->languages as $otherLang)
        @if ($otherLang->code != $lang->code)
            <a href="{{ $otherLang->uri }}">{{ $otherLang->name }}</a>
        @endif
    @endforeach
@endif

{{-- Back search --}}
{{-- Only add a break if there was more info appended to this definition --}}
@if ($def->hasMeaning('eng') ||
    $def->hasLiteralTranslation('eng') ||
    strlen($def->altTitles ||
    count($def->languages) > 1))
    <br>
@endif
<a class="more" href="{{ route('home', ['q' => $def->getPracticalTranslation('eng')]) }}">
    &rarr; more translations for {{ $def->getPracticalTranslation('eng') }}
</a>
