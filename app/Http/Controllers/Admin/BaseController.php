<?php
/**
 * Copyright Di Nkomo(TM) 2016, all rights reserved
 *
 */
namespace App\Http\Controllers\Admin;

use Lang;
use Request;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        $builder = $this->getModel();
        $total = $builder->count();

        $limit = (int) $this->getParam('limit', $this->queryLimit);
        $limit = max($limit, $this->queryLimit);
        $limit = min($limit, $total);
        $this->setParam('limit', $limit);

        $order = $this->getParam('order', $this->defaultOrderColumn);
        $order = in_array($order, $this->supportedOrderColumns) ? $order : $this->defaultOrderColumn;
        $this->setParam('order', $order);

        $dir = strtolower($this->getParam('dir', $this->defaultOrderDirection));
        $dir = in_array($dir, ['asc', 'desc']) ? $dir : $this->defaultOrderDirection;
        $this->setParam('dir', $dir);

        // Add trashed items.
        if (in_array(SoftDeletes::class, class_uses_recursive(get_class($builder)))) {
            $builder = $builder->withTrashed();
        }

        // Paginator
        $page = $this->setParam('page', $this->getParam('page', 1));
        $paginator = $builder->orderBy($order, $dir)->paginate($limit, ['*'], 'page', $page);

        return view("admin.{$this->name}.index", compact([
            'total', 'limit', 'order', 'dir', 'paginator'
        ]));
    }

	/**
	 * Show the form for editing the specified resource.
	 *
     * @param string $id    Definition ID
	 * @return Response
	 */
	public function edit($id)
	{
        // Retrieve the model
        if (!$model = $this->getModel()->find($id)) {
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
        return Request::get($key, Session::get('admin-'. $this->name .'-'. $key, $default));
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
