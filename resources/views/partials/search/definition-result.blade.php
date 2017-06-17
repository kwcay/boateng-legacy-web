
<div class="row">
    <div class="col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 text-left">
        {{ $rank }}.
        <a href="{{ route('definition', $definition->uniqueId) }}" class="definition-title">
            {{ $definition->mainTitle }}
        </a>
        is a <code>{{ $definition->subType }}</code>
        that means <em><a href="{{ route('definition', $definition->uniqueId) }}">
            {{ $definition->translationData->eng->practical }}
        </a></em>

        in <a href="{{ route('language', $definition->mainLanguageCode) }}">
            {{ $definition->mainLanguage->name }}
        </a>
    </div>
</div>
