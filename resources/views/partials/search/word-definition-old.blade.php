
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

{{-- Related definitions --}}
@if (count($def->relatedDefinitionList))
    <span class="meta-label">&rarr; Related:</span>

    <span class="meta-data">
        @foreach ($def->relatedDefinitionList as $related)
            <a href="{{ $related->uri }}">{{ $related->mainTitle }}</a>
        @endforeach
    </span>

    <br>
@endif

{{-- Tags --}}
@if (count($def->tags))
    <span class="meta-label">&rarr; Tagged:</span>

    <span class="meta-data">
        @foreach ($def->tags as $tag)
            <a href="{{ route('home', ['q' => '#'. $tag->title]) }}" class="tag">
                #{{ $tag->title }}
            </a>
        @endforeach
    </span>

    <br>
@endif

{{-- Back search --}}
<span class="meta-label">
    Find more translations for
    <a href="{{ route('home', ['q' => $def->getPracticalTranslation('eng')]) }}">
        {{ $def->getPracticalTranslation('eng') }}
    </a>
</span>
