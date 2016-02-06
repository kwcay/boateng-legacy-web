
<select class="text-center en-text-input" name="subType" id="subType">
    <optgroup label="Parts of Speech">
        <option value="adj"{{ $subType == 'adj' ? ' selected' : '' }}>adjective</option>
        <option value="adv"{{ $subType == 'adv' ? ' selected' : '' }}>adverb</option>
        <option value="conn"{{ $subType == 'conn' ? ' selected' : '' }}>connective</option>
        <option value="ex"{{ $subType == 'ex' ? ' selected' : '' }}>exclamation</option>
        <option value="pre"{{ $subType == 'pre' ? ' selected' : '' }}>preposition</option>
        <option value="pro"{{ $subType == 'pro' ? ' selected' : '' }}>pronoun</option>
        <option value="n"{{ $subType == 'n' ? ' selected' : '' }}>noun</option>
        <option value="v"{{ $subType == 'v' ? ' selected' : '' }}>verb</option>
    </optgroup>
    <optgroup label="Morphemes">
        <option value="prefix"{{ $subType == 'prefix' ? ' selected' : '' }}>prefix</option>
        <option value="suffix"{{ $subType == 'suffix' ? ' selected' : '' }}>suffix</option>
    </optgroup>
</select>
