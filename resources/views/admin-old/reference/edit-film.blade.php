
{{-- Director --}}
<div class="row">
    <input
        type="text"
        name="data[director]"
        id="data[director]"
        class="en-text-input"
        placeholder="directors of the film"
        value="{{ $model->getDataParam('director') }}"
        autocomplete="on"
        required="required">
    <label for="data[director]">Film directors, separated by &quot;;&quot;</label>
</div>

{{-- Title --}}
<div class="row">
    <input
        type="text"
        name="data[title]"
        id="data[title]"
        class="en-text-input"
        placeholder="title of the film"
        value="{{ $model->getDataParam('title') }}"
        autocomplete="off"
        required="required">
    <label for="data[title]">Film title</label>
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
