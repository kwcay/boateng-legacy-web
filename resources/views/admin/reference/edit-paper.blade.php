
{{-- Author --}}
<div class="row">
    <input
        type="text"
        name="data[author]"
        id="data[author]"
        class="en-text-input"
        placeholder="authors of the paper"
        value="{{ $model->getDataParam('author') }}"
        autocomplete="on"
        required="required">
    <label for="data[author]">Paper authors</label>
</div>

{{-- Title --}}
<div class="row">
    <input
        type="text"
        name="data[title]"
        id="data[title]"
        class="en-text-input"
        placeholder="title of the paper"
        value="{{ $model->getDataParam('title') }}"
        autocomplete="off"
        required="required">
    <label for="data[title]">Paper title</label>
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

{{-- Publisher --}}
<div class="row">
    <input
        type="text"
        name="data[publisher]"
        id="data[publisher]"
        class="en-text-input"
        placeholder="publisher of paper"
        value="{{ $model->getDataParam('publisher') }}"
        autocomplete="on">
    <label for="data[publisher]">Publisher</label>
</div>
