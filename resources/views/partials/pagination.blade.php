
<div class="row text-center">
    <div class="col-sm-12">
        {!! $paginator->appends(['order' => $order, 'dir' => $dir, 'limit' => $limit])->links() !!}
    </div>
</div>
