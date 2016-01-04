@extends('layouts.narrow')

@section('body')

	<section>
		<h1>Contribute</h1>

        <div class="col-md-6 col-md-offset-3">
            <ul class="fa-ul">
                <li>
                    <a href="{{ route('language.create') }}">
                        <i class="fa-li fa fa-plus"></i> Add a language
                    </a>
                </li>
            </ul>
        </div>
	</section>

@stop
