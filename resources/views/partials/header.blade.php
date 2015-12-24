
<header class="container-fluid">
    <div class="row">
        <div class="col-xs-10 col-xs-offset-1 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
        	<div class="header">~·~</div>

            @if ($errors->any() || $messages = Session::get('messages'))
            <div class="notices">
                @if ($errors->any())
                    @foreach ($errors->all() as $msg)
                        <div class="error">{!! $msg !!}</div>
                    @endforeach
                @endif
                @if ($messages = Session::pull('messages'))
                    @foreach ($messages as $msg)
                        <div class="message">{!! $msg !!}</div>
                    @endforeach
                @endif
            </div>
            @endif

        </div>
    </div>
</header>
