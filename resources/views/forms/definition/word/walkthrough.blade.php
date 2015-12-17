@extends('layouts.narrow')

@section('body')

    <h1>
        Suggest a new word
        <br>

        <small>
            <a href="{{ route('language.create')  }}">
                &rarr; or click here to suggest a language
            </a>
        </small>
    </h1>
    <br>
    <br>

    <form
        class="edit form"
        method="post"
        name="definition"
        action="{{ route('definition.store') }}">
        
        {!! csrf_field() !!}
        <input type="hidden" name="type" value="{{ $type }}">
        <input type="hidden" name="relations[language][]" value="{{ $lang->code }}">

        {{-- Word --}}
        <div class="row">
            <input
                type="text"
                name="title"
                class="text-input center"
                placeholder="your word"
                autocomplete="off"
                required>
        </div>

        {{-- Sub type --}}
        <div class="row">
            {!! Form::select(
                'subType',
                $word->getSubTypes(),
                $word->rawSubType,
                ['class' => 'en-text-input center']
            ) !!}
        </div>

        <div class="row center">
            is a word in <em>{{ $lang->name }}</em> that means
        </div>

        <!-- Translation -->
        <div class="row">
            <input
                type="text"
                name="relations[practical][eng]"
                class="en-text-input center"
                placeholder="your translation"
                autocomplete="off"
                required>
        </div>

        <div class="row center">
            in English.
        </div>

        <!-- Form actions -->
        <br>
        <br>
        <div class="row center">
            <input type="submit" name="next" value="continue">
            <input type="submit" name="next" value="finish">
            <input type="button" name="cancel" value="return" onclick="return confirm('Cancel?') ? App.redirect('') : false;">
        </div>
    </form>

    <script type="text/javascript">
        // $(document.definition.title).on('blur', function()
        // {
        //     if (!this.value.trim().length) return;
        //
        //     $(this).addClass('loading');
        // });
    </script>

@stop
