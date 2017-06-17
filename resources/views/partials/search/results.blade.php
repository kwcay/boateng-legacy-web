
@if ($results)
    <div class="row">
        <div class="col-msm-12">
            Showing <em>{{ number_format(count($results)) }}</em>
            results for <em>{{ $query }}</em>.
        </div>
    </div>

    @foreach ($results as $result)
        @include('partials.search.'.$result->resourceType.'-result', [
            'rank'                  => $loop->index + 1,
            $result->resourceType   => $result,
        ])
    @endforeach

@elseif ($query)
    <div class="row">
        <div class="col-md-10 col-md-offset-1 col-lg-6 col-lg-offset-3">
            Couldn't find anything for <em>{{ $query }}</em> :/
        </div>
    </div>
@endif
