
@if (count($errors) > 0)
    <div class="container-fluid text-left">
        @foreach ($errors->all() as $error)
            <div class="row">
                <div class="col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-3 alert alert-warning">
                    {{ $error }}
                </div>
            </div>
        @endforeach
    </div>
@endif
