
{{-- Given name --}}
<div class="row">
    <input
        type="text"
        name="data[givenName]"
        id="data[givenName]"
        class="en-text-input"
        placeholder="given name of person"
        value="{{ $model->getDataParam('givenName') }}"
        autocomplete="on"
        required="required">
    <label for="data[givenName]">Given name</label>
</div>

{{-- Other names --}}
<div class="row">
    <input
        type="text"
        name="data[otherNames]"
        id="data[otherNames]"
        class="en-text-input"
        placeholder="other names of person"
        value="{{ $model->getDataParam('otherNames') }}"
        autocomplete="on"
        required="required">
    <label for="data[otherNames]">Other names</label>
</div>

{{-- Nickname --}}
<div class="row">
    <input
        type="text"
        name="data[alias]"
        id="data[alias]"
        class="en-text-input"
        placeholder="nickname of person"
        value="{{ $model->getDataParam('alias') }}"
        autocomplete="on">
    <label for="data[alias]">Nicknames, separated by &quot;;&quot;</label>
</div>

{{-- Date of birth --}}
<div class="row">
    <input
        type="date"
        name="data[dob]"
        id="data[dob]"
        class="en-text-input"
        placeholder="date of birth"
        value="{{ $model->getDataParam('dob') }}"
        autocomplete="off">
    <label for="data[dob]">Date of birth</label>
</div>

{{-- City --}}
<div class="row">
    <input
        type="text"
        name="data[cityOfBirth]"
        id="data[cityOfBirth]"
        class="en-text-input"
        placeholder="city where person was born"
        value="{{ $model->getDataParam('cityOfBirth') }}"
        autocomplete="on">
    <label for="data[cityOfBirth]">City</label>
</div>

{{-- Country --}}
<div class="row">
    <input
        type="text"
        name="data[countryOfBirth]"
        id="data[countryOfBirth]"
        class="en-text-input"
        placeholder="country where person was born"
        value="{{ $model->getDataParam('countryOfBirth') }}"
        autocomplete="on">
    <label for="data[countryOfBirth]">Country</label>
</div>
