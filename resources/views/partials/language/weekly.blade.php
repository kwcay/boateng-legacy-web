
@if ($language)
<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="shaded-well" style="background-image:url(/img/bg/9ad291d387f72a04a57e2b4c8945f945-640x480.jpg);">
            <a href="{{ route('language', $language->code) }}" class="card-btn shade-50">
                Language of the week:

                <h3>{{ $language->name }}</h3>
            </a>
        </div>
    </div>
</div>
@endif
