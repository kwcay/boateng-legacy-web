
@if (isset($definition))
    @lang('language.know-a-thing-or-two', [
        'language' => $definition->getLanguageString('or')
    ])

    @lang('definition.add-word-expression', [
        'word'          => $definition->createRoute(),
        'expression'    => $definition->createRoute('expression'),
    ])
@elseif (isset($language))
    @lang('language.know-a-thing-or-two', [
        'language' => $language->getFirstName()
    ])

    @lang('definition.add-word-expression', [
        'word'          => route('definition.create', ['lang' => $language->code, 'type' => 'word']),
        'expression'    => route('definition.create', ['lang' => $language->code, 'type' => 'expression']),
    ])
@endif
