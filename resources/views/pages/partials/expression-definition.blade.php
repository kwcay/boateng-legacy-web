
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
    <br>
@endif

{{-- Append literal meaning --}}
@if ($def->hasLiteralTranslation('eng'))
    <span class="meta-label">&rarr; Literally:</span>
    <span class="meta-data">{{ $def->getLiteralTranslation('eng') }}</span>
    <br>
@endif

{{-- Append alternative spellings --}}
@if (count($def->titles) > 1)
    <span class="meta-label">&rarr; Spellings:</span>
    <span class="meta-data">{{ $def->titles->implode('title', ', ') }}</span>
    <br>
@endif

{{-- Add language information --}}
@if (count($def->languages) > 1)
    <span class="meta-label">&rarr; Also an expression in:</span>

    <span class="meta-data">
        @foreach ($def->languages as $otherLang)
            @if ($otherLang->code != $lang->code)
                <a href="{{ $otherLang->uri }}">{{ $otherLang->name }}</a>
            @endif
        @endforeach
    </span>

    <br>
@endif

{{-- Tags --}}
@if (count($def->tags))
    <span class="meta-label">&rarr; Tagged:</span>

    <span class="meta-data">
        @foreach ($def->tags as $tag)
            <a href="#" class="tag">
                #{{ $tag->title }}
            </a>
        @endforeach
    </span>

    <br>
@endif

{{-- Word by word lookup --}}
<span class="meta-label">
    Lookup the <a href="{{ $lang->uri }}">{{ $lang->name }}</a> words:

    @foreach (@explode(' ', $def->titles[0]->title) as $word)
        <a
            href="{{ route('language.show', ['code' => $def->mainLanguage->code, 'q' => $word]) }}"
            target="_blank"
            style="margin: 0 5px;">

            {{ $word }}
        </a>
    @endforeach
</span>
