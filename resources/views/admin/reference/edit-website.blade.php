
{{-- Author --}}
<div class="row">
    <input
        type="text"
        name="data[author]"
        id="data[author]"
        class="en-text-input"
        placeholder="authors of the website"
        value="{{ $model->getDataParam('author') }}"
        autocomplete="on">
    <label for="data[author]">Website authors, separated by &quot;;&quot;</label>
</div>

{{-- Page title --}}
<div class="row">
    <input
        type="text"
        name="data[title]"
        id="data[title]"
        class="en-text-input"
        placeholder="title of the web page"
        value="{{ $model->getDataParam('title') }}"
        autocomplete="off"
        required="required">
    <label for="data[title]">Title of web page</label>
</div>

{{-- Site name --}}
<div class="row">
    <input
        type="text"
        name="data[name]"
        id="data[name]"
        class="en-text-input"
        placeholder="name of the website"
        value="{{ $model->getDataParam('name') }}"
        autocomplete="on"
        required="required">
    <label for="data[name]">Website name</label>
</div>

{{-- URL --}}
<div class="row">
    <input
        type="url"
        name="data[url]"
        id="data[url]"
        class="en-text-input"
        placeholder="http://www.example.com"
        value="{{ $model->getDataParam('url') }}"
        autocomplete="on"
        required="required">
    <label for="data[url]">URL of web page</label>
</div>

{{-- Date --}}
<div class="row">
    <input
        type="date"
        name="data[date]"
        id="data[date]"
        class="en-text-input"
        placeholder="retrieved on..."
        value="{{ $model->getDataParam('date') }}"
        autocomplete="off">
    <label for="data[date]">Retrieved date</label>
</div>
