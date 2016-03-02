
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

{{-- Word by word lookup --}}
<span class="meta-label">
    Lookup the <a href="{{ $lang->uri }}">{{ $lang->name }}</a> words:

    @foreach (@explode(' ', $def->titles[0]->title) as $word)
        <a
            href="{{ route('language.show', ['code' => $def->mainLanguage->code, 'q' => $word]) }}"
            style="margin: 0 5px;">

            {{ $word }}
        </a>
    @endforeach
</span>
