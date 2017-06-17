@extends('layouts.narrow')

@section('body')

	<section>
		<h1>
            <form class="form" action="" method="GET" name="referenceTypeForm">
                <div class="row">
                    <div class="col-sm-12">
                        Create a new

                        {!!
                            Form::select('type', \App\Models\Reference::$types, Request::get('type', 'book'), [
                                'class' => 'inline text-center',
                                'onchange' => 'return updateReferenceType()',
                            ])
                        !!}

                        reference
                    </div>
                </div>
            </form>

            <small>
                And help improve @lang('branding.title') for everyone.
            </small>
        </h1>

		<form class="form edit" method="post" action="{{ route('reference.store') }}">
            <input type="hidden" name="type" value="{{ Request::get('type', 'book') }}">
			{!! csrf_field() !!}

            @include('forms.reference.'. Request::get('type', 'book'))

        	{{-- Form actions --}}
			<div class="row center">
				<input type="submit" name="return" value="continue">
			</div>
		</form>
	</section>

    <script type="text/javascript">

        var updateReferenceType = function() {

            var form = $(document.referenceTypeForm);

            form.submit();

            form.find('select[name="type"]').attr('disabled', true);
        };
    </script>

@stop
