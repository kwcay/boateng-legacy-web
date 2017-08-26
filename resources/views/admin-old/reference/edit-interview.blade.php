
{{-- Interviewee --}}
<div class="row">
    <input
        type="text"
        name="data[interviewee]"
        id="data[interviewee]"
        class="en-text-input"
        placeholder="name of interviewees"
        value="{{ $model->getDataParam('interviewee') }}"
        autocomplete="on"
        required="required">
    <label for="data[interviewee]">Name of interviewees, separated by &quot;;&quot;</label>
</div>

{{-- Interviewer --}}
<div class="row">
    <input
        type="text"
        name="data[interviewer]"
        id="data[interviewer]"
        class="en-text-input"
        placeholder="name of interviewers"
        value="{{ $model->getDataParam('interviewer') }}"
        autocomplete="on"
        required="required">
    <label for="data[interviewer]">Name of interviewers, separated by &quot;;&quot;</label>
</div>

{{-- Title --}}
<div class="row">
    <input
        type="text"
        name="data[title]"
        id="data[title]"
        class="en-text-input"
        placeholder="title of the interview"
        value="{{ $model->getDataParam('title') }}"
        autocomplete="off"
        required="required">
    <label for="data[title]">Interview title</label>
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
