
{{-- Title --}}
<div class="row">
    <input
        type="text"
        name="data[title]"
        id="data[title]"
        class="en-text-input"
        placeholder="title of the song"
        value="{{ $model->getDataParam('title') }}"
        autocomplete="off"
        required="required">
    <label for="data[title]">Song title</label>
</div>

{{-- Main artists --}}
<div class="row">
    <input
        type="text"
        name="data[mainArtist]"
        id="data[mainArtist]"
        class="en-text-input"
        placeholder="main artists"
        value="{{ $model->getDataParam('mainArtist') }}"
        autocomplete="on"
        required="required">
    <label for="data[mainArtist]">Main artists, separated by &quot;;&quot;</label>
</div>

{{-- Supporting artists --}}
<div class="row">
    <input
        type="text"
        name="data[supportArtist]"
        id="data[supportArtist]"
        class="en-text-input"
        placeholder="supporting artists"
        value="{{ $model->getDataParam('supportArtist') }}"
        autocomplete="on">
    <label for="data[supportArtist]">Supporting artists, separated by &quot;;&quot;</label>
</div>

{{-- Date --}}
<div class="row">
    <input
        type="date"
        name="data[date]"
        id="data[date]"
        class="en-text-input"
        placeholder="publication date"
        value="{{ $model->getDataParam('date') }}"
        autocomplete="off">
    <label for="data[date]">Publication date</label>
</div>

{{-- Label --}}
<div class="row">
    <input
        type="text"
        name="data[label]"
        id="data[label]"
        class="en-text-input"
        placeholder="name of label"
        value="{{ $model->getDataParam('label') }}"
        autocomplete="on">
    <label for="data[label]">Publishing label</label>
</div>
