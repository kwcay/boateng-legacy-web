<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Http\Controllers\Admin;

use Request;
use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    /**
     *
     */
    protected $name;

    /**
     *
     */
    protected $queryLimit = 20;

    /**
     *
     */
    protected $supportedOrderColumns = [];

    /**
     *
     */
    protected $defaultOrderColumn = 'id';

    /**
     *
     */
    protected $defaultOrderDirection = 'desc';

    /**
     *
     */
    public function __construct()
    {
        // Performance check.
        if (!$this->name) {
            throw new Exception('Invalid controller name.');
        }
    }

    /**
     * Displays a listing of the resource
     *
     * @return View
     */
    public function index()
    {
        // Query parameters
        $builder = $this->getModelQueryBuilder();
        $total = $builder->count();

        $limit = (int) Request::get('limit', $this->queryLimit);
        $limit = max($limit, $this->queryLimit);
        $limit = min($limit, $total);

        $order = Request::get('order', $this->defaultOrderColumn);
        $order = in_array($order, $this->supportedOrderColumns) ? $order : $this->defaultOrderColumn;

        $dir = strtolower(Request::get('dir', $this->defaultOrderDirection));
        $dir = in_array($dir, ['asc', 'desc']) ? $dir : $this->defaultOrderDirection;

        $paginator = $builder->orderBy($order, $dir)->paginate($limit);

        return view("admin.{$this->name}.index", compact([
            'total', 'limit', 'order', 'dir', 'paginator'
        ]));
    }

    /**
     *
     */
    protected function getModelQueryBuilder()
    {
        throw new Exception('Invalid model query builder.');
    }
}
