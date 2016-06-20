@extends('layouts.narrow')

@section('title', trans('branding.mt_title') .': '. trans('branding.mt_tag_line'))
@section('description', trans('branding.mt_tag_line'))
@section('keywords', 'machine, translation, automatic, engine')

@section('body')

	<section>
		<h1>
            @lang('branding.mt_title')
            <br>

            <small>
                @lang('branding.mt_tag_line')
            </small>
        </h1>

        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-lg-8 col-lg-offset-2 text-center">
                Safoa is a machine translation engine for those languages outside the mainstream,
                i.e. 95% of all languages.
                <br>
                <br>

                <div class="emphasis">
                    Coming soon, stay tuned ;)
                </div>
            </div>
        </div>
	</section>

@stop
