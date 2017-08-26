
{{-- Author --}}
<div class="row">
    <input
        type="text"
        name="data[author]"
        id="data[author]"
        class="en-text-input"
        placeholder="authors of the audio clip"
        value="{{ $model->getDataParam('author') }}"
        autocomplete="on"
        required="required">
    <label for="data[author]">Audio clip authors</label>
</div>

{{-- Title --}}
<div class="row">
    <input
        type="text"
        name="data[title]"
        id="data[title]"
        class="en-text-input"
        placeholder="title of the audio clip"
        value="{{ $model->getDataParam('title') }}"
        autocomplete="off"
        required="required">
    <label for="data[title]">Audio clip title</label>
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
