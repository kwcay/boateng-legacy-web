
{{-- Author --}}
<div class="row">
    <input
        type="text"
        name="data[author]"
        id="data[author]"
        class="en-text-input"
        placeholder="authors of the book"
        value="{{ $model->getDataParam('author') }}"
        autocomplete="on"
        required="required">
    <label for="data[author]">Book authors</label>
</div>

{{-- Title --}}
<div class="row">
    <input
        type="text"
        name="data[title]"
        id="data[title]"
        class="en-text-input"
        placeholder="title of the book"
        value="{{ $model->getDataParam('title') }}"
        autocomplete="off"
        required="required">
    <label for="data[title]">Book title</label>
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
        placeholder="publisher of book"
        value="{{ $model->getDataParam('publisher') }}"
        autocomplete="on">
    <label for="data[publisher]">Publisher</label>
</div>

{{-- ISBN --}}
<div class="row">
    <input
        type="text"
        name="data[isbn]"
        id="data[isbn]"
        class="en-text-input"
        placeholder="10-digit ISBN number"
        value="{{ $model->getDataParam('isbn') }}"
        autocomplete="on">
    <label for="data[isbn]">ISBN</label>
</div>

{{-- ISBN-13 --}}
<div class="row">
    <input
        type="text"
        name="data[isbn-13]"
        id="data[isbn-13]"
        class="en-text-input"
        placeholder="13-digit ISBN number"
        value="{{ $model->getDataParam('isbn-13') }}"
        autocomplete="on">
    <label for="data[isbn-13]">ISBN-13</label>
</div>

{{-- City --}}
<div class="row">
    <input
        type="text"
        name="data[city]"
        id="data[city]"
        class="en-text-input"
        placeholder="city where book was published"
        value="{{ $model->getDataParam('city') }}"
        autocomplete="on">
    <label for="data[city]">City</label>
</div>
