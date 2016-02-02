
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

    {{-- Word by word lookup --}}
    <br>
    <span class="more">
        &rarr; lookup the words:

        @foreach (@explode(' ', $def->title) as $word)
            <a
                href="{{ route('language.show', ['code' => $def->mainLanguage->code, 'q' => $word]) }}"
                style="margin: 0 5px;">
                
                {{ $word }}
            </a>
        @endforeach
    </span>
</h3>
