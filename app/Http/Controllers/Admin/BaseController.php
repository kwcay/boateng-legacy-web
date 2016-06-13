<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Http\Controllers\Admin;

use Lang;
use Session;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseController extends Controller
{
    /**
     * @var string
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
     * @param Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        // Performance check.
        if (!$this->name) {
            throw new Exception('Invalid controller name.');
        }

        $this->request = $request;
    }

    /**
     * Displays a listing of the resource
     *
     * @return View
     */
    public function index()
    {
        // Query parameters
        $builder = $this->getModel();
        $total = $builder->count();

        $limit = (int) $this->getParam('limit', $this->defaultQueryLimit);
        $limit = max($limit, 1);
        $limit = min($limit, $total);
        $this->setParam('limit', $limit);

        $limits = [];
        if ($total > 10)    $limits[10] = 10;
        if ($total > 20)    $limits[20] = 20;
        if ($total > 30)    $limits[30] = 30;
        if ($total > 50)    $limits[50] = 50;
        if ($total > 100)    $limits[100] = 100;
        $limits[$total] = $total;

        $orders = collect($this->supportedOrderColumns);
        $order = $this->getParam('order', $this->defaultOrderColumn);
        $order = $orders->has($order) ? $order : $this->defaultOrderColumn;
        // $order = in_array($order, $this->supportedOrderColumns) ? $order : $this->defaultOrderColumn;
        $this->setParam('order', $order);

        $dirs = collect(['asc' => 'ascending', 'desc' => 'descending']);
        $dir = strtolower($this->getParam('dir', $this->defaultOrderDirection));
        $dir = $dirs->has($dir) ? $dir : $this->defaultOrderDirection;
        // $dir = in_array($dir, ['asc', 'desc']) ? $dir : $this->defaultOrderDirection;
        $this->setParam('dir', $dir);

        // Add trashed items.
        if (in_array(SoftDeletes::class, class_uses_recursive(get_class($builder)))) {
            $builder = $builder->withTrashed();
        }

        // Paginator
        $page = $this->setParam('page', $this->getParam('page', 1));
        $paginator = $builder->orderBy($order, $dir)->paginate($limit, ['*'], 'page', $page);

        return view("admin.{$this->name}.index", compact([
            'total',
            'limit',
            'limits',
            'order',
            'orders',
            'dir',
            'dirs',
            'paginator'
        ]));
    }

	/**
	 * Show the form for editing the specified resource.
	 *
     * @param mixed $id    ID or Eloquent model.
	 * @return Response
	 */
	public function edit($id)
	{
        // If we already have an instance of the model, great.
        if (is_a($id, 'Illuminate\Database\Eloquent\Model'))
        {
            $model = $id;
        }

        // Performance check.
        elseif (!is_numeric($id) || !strlen($id))
        {
            abort(404);
        }

        // Retrieve the model by ID.
        elseif (!$model = $this->getModel()->find($id))
        {
            abort(404);
        }

        return view("admin.{$this->name}.edit", compact([
            'model'
        ]));
    }

	/**
	 * Stores a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
    {
        abort(501);
    }

    /**
     *
     */
    protected function getModel()
    {
        $className = '\\App\\Models\\'. ucfirst($this->name);

        return new $className;
    }

    /**
     * Retrieves a parameter from the request, or the session
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getParam($key, $default = null)
    {
        return $this->request->get($key, Session::get('admin-'. $this->name .'-'. $key, $default));
    }

    /**
     * Saves a parameter value to the session.
     *
     * @param string $key
     * @param mixed $value
     * @return ??
     */
    protected function setParam($key, $value = null)
    {
        Session::put('admin-'. $this->name .'-'. $key, $value);

        return $value;
    }
}
