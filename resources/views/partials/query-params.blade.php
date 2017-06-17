
<?php $id = substr(explode(' ', microtime())[0], 2) ?>

<form class="form" action="" method="GET" name="queryParamsForm{{ $id }}">
    <div class="row text-center">
        <div class="col-sm-12">
            Showing

            {!!
                Form::select('limit', $limits, $limit, [
                    'class' => 'inline text-center',
                    'onchange' => 'return updateQueryParams'. $id .'()',
                ])
            !!}

            of

            <em>
                {{ number_format($paginator->total()) }}
            </em>

            results, ordered by

            {!!
                Form::select('order', $orders, $order, [
                    'class' => 'inline text-center',
                    'onchange' => 'return updateQueryParams'. $id .'()',
                ])
            !!},

            {!!
                Form::select('dir', $dirs, $dir, [
                    'class' => 'inline text-center',
                    'onchange' => 'return updateQueryParams'. $id .'()',
                ])
            !!}
        </div>
    </div>

    <input type="hidden" name="page" value="{{ $paginator->currentPage() }}">
</form>

<script type="text/javascript">

    var updateQueryParams{{ $id }} = function() {

        var form = $(document.queryParamsForm{{ $id }});

        form.submit();

        form.find('select[name="limit"]').attr('disabled', true);
        form.find('select[name="order"]').attr('disabled', true);
        form.find('select[name="dir"]').attr('disabled', true);
    };
</script>
