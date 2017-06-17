
{{-- Creator --}}
<div class="row">
    <input
        type="text"
        name="data[creator]"
        id="data[creator]"
        class="en-text-input"
        placeholder="creators of the video clip"
        value="{{ $model->getDataParam('creator') }}"
        autocomplete="on"
        required="required">
    <label for="data[creator]">Creators of video clip, separated by &quot;;&quot;</label>
</div>

{{-- Title --}}
<div class="row">
    <input
        type="text"
        name="data[title]"
        id="data[title]"
        class="en-text-input"
        placeholder="title of the video clip"
        value="{{ $model->getDataParam('title') }}"
        autocomplete="off"
        required="required">
    <label for="data[title]">Video clip title</label>
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
