<header>
	<div class="header">~·~</div>
    
    @if ($errors->any() || $messages = Session::get('messages'))
    <div class="notices">
        @if ($errors->any())
            @foreach ($errors->all() as $msg)
                <div class="error">{{ $msg }}</div>
            @endforeach
        @endif
        @if ($messages = Session::pull('messages'))
            @foreach ($messages as $msg)
                <div class="message">{{ $msg }}</div>
            @endforeach
        @endif
    </div>
    @endif
</header>
