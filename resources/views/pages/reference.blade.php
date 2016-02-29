@extends('layouts.narrow')

@section('body')

    <h1>
        Di Nkɔmɔ<br>

        <small>
            A Cultural Reference.
        </small>
    </h1>

    {{-- Form --}}
    <form name="dictionary" class="search form">
        <div class="row">
            <div class="col-xs-12 col-lg-10 col-lg-offset-1">
                <div class="input-wrapper">
                    <input
                        name="clear"
                        type="button"
                        value="&#10005;"
                        class="remove-btn-style">

                    <input
                        name="q"
                        type="text"
                        value="{{ Request::input('q') }}"
                        placeholder="start here"
                        autocomplete="off">

                    <input
                        type="submit"
                        value="&#10163;"
                        class="remove-btn-style">
                </div>
            </div>
        </div>
    </form>

    {{-- Results --}}
    <div id="results"></div>

    {{-- Twitter pitch --}}
    <div class="col-sm-12 text-center">
        Di Nkɔmɔ is a free <a href="{{ route('about') }}">cultural repository</a> <br>
        for the gems of the world.
    </div>

    <script type="text/javascript">
    new DiNkomoDictionary();
    </script>

@stop
